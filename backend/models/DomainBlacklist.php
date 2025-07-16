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
                    d.added_at,
                    u.username as added_by,
                    COUNT(r.id) as report_count
                FROM domain_blacklist d
                LEFT JOIN users u ON d.added_by = u.id
                LEFT JOIN domain_reports r ON d.domain = r.domain
                GROUP BY d.id, d.domain, d.reason, d.added_at, u.username
                ORDER BY d.added_at DESC";
                
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function addDomain($domain, $reason, $userId) {
        try {
            $this->db->beginTransaction();
            
            // First, add to domain_reports
            $stmt = $this->db->prepare(
                "INSERT INTO domain_reports (domain, reported_by, reason) VALUES (?, ?, ?)"
            );
            $stmt->execute([$domain, $userId, $reason]);
            
            // Add to blacklist
            $stmt = $this->db->prepare(
                "INSERT INTO domain_blacklist (domain, reason, added_by) VALUES (?, ?, ?)"
            );
            $stmt->execute([$domain, $reason, $userId]);
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
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

    public function getAllDomainsWithReports() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    d.*,
                    u.username as added_by,
                    COUNT(r.id) as report_count
                FROM domain_blacklist d
                LEFT JOIN users u ON d.added_by = u.id
                LEFT JOIN domain_reports r ON d.domain = r.domain
                GROUP BY d.id
                ORDER BY d.added_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting domains with reports: " . $e->getMessage());
            return [];
        }
    }
} 