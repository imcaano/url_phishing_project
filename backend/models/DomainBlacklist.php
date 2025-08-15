<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class DomainBlacklist {
    private $db;
    
    public function __construct() {
        $this->db = Database::getDB();
    }
    
    public function getAllDomains() {
        $query = "SELECT 
                    d.domain,
                    d.reason,
                    d.id,
                    d.added_at,
                    COUNT(r.id) as report_count
                FROM domain_blacklist d
                LEFT JOIN domain_reports r ON d.domain = r.domain
                GROUP BY d.id, d.domain, d.reason, d.added_at
                ORDER BY d.id DESC";
                
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addDomain($domain, $reason, $userId) {
        try {
            error_log("DomainBlacklist::addDomain: Starting to add domain '$domain' with reason '$reason' and userId $userId");
            
            // Check for duplicate first (before starting transaction)
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM domain_blacklist WHERE domain = ?");
            $stmt->execute([$domain]);
            $existingCount = $stmt->fetchColumn();
            error_log("DomainBlacklist::addDomain: Existing count for domain '$domain': $existingCount");
            
            if ($existingCount > 0) {
                error_log("DomainBlacklist::addDomain: Domain '$domain' is already blacklisted, throwing exception");
                throw new \Exception('Domain is already blacklisted.');
            }
            
            error_log("DomainBlacklist::addDomain: Starting transaction");
            $this->db->beginTransaction();
            
            // First, add to domain_reports
            error_log("DomainBlacklist::addDomain: Adding to domain_reports table");
            $stmt = $this->db->prepare(
                "INSERT INTO domain_reports (domain, reported_by, reason) VALUES (?, ?, ?)"
            );
            $reportResult = $stmt->execute([$domain, $userId, $reason]);
            error_log("DomainBlacklist::addDomain: domain_reports insert result: " . ($reportResult ? 'true' : 'false'));
            
            // Add to blacklist
            error_log("DomainBlacklist::addDomain: Adding to domain_blacklist table");
            $stmt = $this->db->prepare(
                "INSERT INTO domain_blacklist (domain, reason, added_by) VALUES (?, ?, ?)"
            );
            $blacklistResult = $stmt->execute([$domain, $reason, $userId]);
            error_log("DomainBlacklist::addDomain: domain_blacklist insert result: " . ($blacklistResult ? 'true' : 'false'));
            
            if ($reportResult && $blacklistResult) {
                error_log("DomainBlacklist::addDomain: Both inserts successful, committing transaction");
                $this->db->commit();
                error_log("DomainBlacklist::addDomain: Transaction committed successfully");
                return true;
            } else {
                error_log("DomainBlacklist::addDomain: One or both inserts failed, rolling back transaction");
                $this->db->rollBack();
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("DomainBlacklist::addDomain: Exception occurred: " . $e->getMessage());
            // Only rollback if there's an active transaction
            if ($this->db->inTransaction()) {
                error_log("DomainBlacklist::addDomain: Rolling back transaction due to exception");
                $this->db->rollBack();
            }
            throw $e;
        }
    }
    
    public function getDomainReports($domain) {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) as report_count FROM domain_reports WHERE domain = ?"
        );
        $stmt->execute([$domain]);
        return $stmt->fetch(PDO::FETCH_ASSOC)['report_count'];
    }

    public function getTotalDomains() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM domain_blacklist");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting total blacklisted domains: " . $e->getMessage());
            return 0;
        }
    }

    public function deleteDomain($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM domain_blacklist WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            error_log("Error deleting domain: " . $e->getMessage());
            return false;
        }
    }

    public function getDomainById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM domain_blacklist WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting domain by ID: " . $e->getMessage());
            return false;
        }
    }

    public function getDomainByDomain($domain) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM domain_blacklist WHERE domain = ?");
            $stmt->execute([$domain]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting domain by domain name: " . $e->getMessage());
            return false;
        }
    }

    public function getAllDomainsWithReports() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    d.id,
                    d.domain,
                    d.reason,
                    'reported' as status,
                    COUNT(r.id) as report_count,
                    u.username as added_by,
                    d.added_at
                FROM domain_blacklist d
                LEFT JOIN users u ON d.added_by = u.id
                LEFT JOIN domain_reports r ON d.domain = r.domain
                GROUP BY d.id, d.domain, d.reason, u.username, d.added_at
                ORDER BY d.added_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting domains with reports: " . $e->getMessage());
            return [];
        }
    }

    public function restoreDomain($id) {
        try {
            $this->db->beginTransaction();
            
            // Get domain info before deletion
            $stmt = $this->db->prepare("SELECT domain FROM domain_blacklist WHERE id = ?");
            $stmt->execute([$id]);
            $domain = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$domain) {
                $this->db->rollBack();
                return false;
            }
            
            // Remove from blacklist
            $stmt = $this->db->prepare("DELETE FROM domain_blacklist WHERE id = ?");
            $stmt->execute([$id]);
            
            // Update scanned_domains table to mark as safe
            $stmt = $this->db->prepare("
                UPDATE scanned_domains 
                SET is_blacklisted = 0, 
                    blacklist_date = NULL, 
                    blacklist_reason = NULL 
                WHERE domain = ?
            ");
            $stmt->execute([$domain['domain']]);
            
            // Add to safe_domains table if it doesn't exist
            $stmt = $this->db->prepare("
                INSERT IGNORE INTO safe_domains (domain, restored_by, restored_at, reason) 
                VALUES (?, ?, NOW(), 'Restored from blacklist by admin')
            ");
            $stmt->execute([$domain['domain'], $_SESSION['user_id'] ?? 1]);
            
            $this->db->commit();
            return true;
            
        } catch (\PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Error restoring domain: " . $e->getMessage());
            return false;
        }
    }
} 