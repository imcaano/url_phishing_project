<?php
namespace App\Controllers;

use App\Config\Database;
use App\Models\URLScan;
use PDO;

class ImportController {
    private $db;
    private $urlScan;
    private $maxDomains = 50; // Maximum domains per import

    public function __construct() {
        $this->db = Database::getDB();
        $this->urlScan = new URLScan();
        // Increase PHP execution time limit
        set_time_limit(300); // 5 minutes
    }

    public function index() {
        $scanResults = [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['domains'])) {
                // Handle domain scanning
                $scanResults = $this->scanDomains($_POST['domains']);
                
                // Auto-import to reports if requested
                if (isset($_POST['add_to_blacklist']) && $_POST['add_to_blacklist'] == '1') {
                    $this->autoImportToReports($scanResults);
                }
                
                // Set success message
                if (!empty($scanResults)) {
                    $_SESSION['success'] = true;
                    $_SESSION['message'] = "Successfully scanned " . count($scanResults) . " domains.";
                }
            } elseif (isset($_POST['import_all'])) {
                // Handle manual import all to reports
                if (isset($_SESSION['scan_results'])) {
                    $this->importAllToReports($_SESSION['scan_results']);
                    $_SESSION['success'] = true;
                    $_SESSION['message'] = "Successfully imported all domains to reports.";
                    unset($_SESSION['scan_results']);
                }
            }
        }
        
        // Store scan results in session for potential re-import
        if (!empty($scanResults)) {
            $_SESSION['scan_results'] = $scanResults;
        }
        
        // Display the import form with results
        require_once __DIR__ . '/../views/admin/import.php';
        
        // Clear scan results from session after displaying to prevent showing on refresh
        if (isset($_SESSION['scan_results'])) {
            unset($_SESSION['scan_results']);
        }
    }

    private function scanDomains($domainsText) {
        $domains = $this->parseDomains($domainsText);
        $results = [];
        
        // Get user ID from session
        $userId = $_SESSION['user_id'] ?? 1; // Default to admin user ID 1 if not set
        
        if (count($domains) > $this->maxDomains) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = "Maximum {$this->maxDomains} domains allowed per import. Please reduce the number of domains.";
            return [];
        }
        
        foreach ($domains as $domain) {
            try {
                // Clean the domain
                $cleanDomain = $this->cleanDomain($domain);
                if (empty($cleanDomain)) continue;
                
                // Add protocol if missing
                $url = (preg_match('~^https?://~', $cleanDomain)) ? $cleanDomain : 'https://' . $cleanDomain;
                
                // Scan the domain using ML model
                $scanResult = $this->scanWithML($url);
                
                if ($scanResult && $scanResult['status'] === 'success') {
                    // Get WHOIS data from R1 system
                    $whoisData = $this->getWhoisFromR1($url);
                    if (!empty($whoisData)) {
                        $scanResult['whois_info'] = $whoisData;
                    }
                    
                    // Auto-add phishing domains to blacklist
                    if (isset($scanResult['prediction']) && $scanResult['prediction'] === 'phishing') {
                        $this->autoAddToBlacklist($cleanDomain);
                        $scanResult['auto_blacklisted'] = true;
                        $scanResult['blacklist_message'] = "Domain automatically added to blacklist as phishing.";
                    }
                    
                    // Save import history
                    $this->saveImportHistory($cleanDomain, $scanResult, $userId);
                    
                    $results[] = [
                        'domain' => $cleanDomain,
                        'status' => $this->determineStatus($scanResult),
                        'confidence' => $scanResult['confidence_score'] ?? 'N/A',
                        'whois' => $scanResult['whois_info'] ?? [],
                        'scan_data' => $scanResult,
                        'prediction' => $scanResult['prediction'] ?? 'unknown',
                        'auto_blacklisted' => $scanResult['auto_blacklisted'] ?? false
                    ];
                } else {
                    $results[] = [
                        'domain' => $cleanDomain,
                        'status' => 'error',
                        'confidence' => 'N/A',
                        'whois' => [],
                        'scan_data' => null,
                        'prediction' => 'unknown',
                        'auto_blacklisted' => false
                    ];
                }
                
            } catch (\Exception $e) {
                error_log("Error scanning domain {$domain}: " . $e->getMessage());
                $results[] = [
                    'domain' => $domain,
                    'status' => 'error',
                    'confidence' => 'N/A',
                    'whois' => [],
                    'scan_data' => null
                ];
            }
        }
        
        return $results;
    }

    private function parseDomains($domainsText) {
        // Split by commas, newlines, or semicolons
        $domains = preg_split('/[\s,;]+/', $domainsText, -1, PREG_SPLIT_NO_EMPTY);
        
        // Clean and filter domains
        $cleanDomains = [];
        foreach ($domains as $domain) {
            $cleanDomain = trim($domain);
            if (!empty($cleanDomain)) {
                $cleanDomains[] = $cleanDomain;
            }
        }
        
        return array_unique($cleanDomains);
    }

    private function cleanDomain($domain) {
        // Remove protocol
        $domain = preg_replace('/^https?:\/\//i', '', $domain);
        
        // Remove www prefix
        $domain = preg_replace('/^www\./i', '', $domain);
        
        // Remove trailing slash
        $domain = rtrim($domain, '/');
        
        // Basic domain validation
        if (filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
            return $domain;
        }
        
        return '';
    }

    private function determineStatus($scanResult) {
        if (isset($scanResult['status']) && $scanResult['status'] === 'not_found') {
            return 'not_found';
        }
        
        // Check ML prediction first
        if (isset($scanResult['prediction'])) {
            return $scanResult['prediction'];
        }
        
        // Fallback to old R1 model
        if (isset($scanResult['is_phishing'])) {
            return $scanResult['is_phishing'] ? 'phishing' : 'safe';
        }
        
        return 'unknown';
    }

    private function autoImportToReports($scanResults) {
        $importedCount = 0;
        
        foreach ($scanResults as $result) {
            if ($result['status'] === 'phishing') {
                try {
                    // Add to domain_reports
                    $this->addToReports($result);
                    
                    // Add to blacklist if auto-blacklist is enabled
                    $this->addToBlacklist($result);
                    
                    $importedCount++;
                } catch (\Exception $e) {
                    error_log("Error auto-importing domain {$result['domain']}: " . $e->getMessage());
                }
            }
        }
        
        if ($importedCount > 0) {
            $_SESSION['success'] = true;
            $_SESSION['message'] = "Successfully imported {$importedCount} phishing domains to reports and blacklist.";
        }
    }

    private function importAllToReports($scanResults) {
        $importedCount = 0;
        
        foreach ($scanResults as $result) {
            try {
                // Add to domain_reports
                $this->addToReports($result);
                
                // Add to blacklist if it's phishing
                if ($result['status'] === 'phishing') {
                    $this->addToBlacklist($result);
                }
                
                $importedCount++;
            } catch (\Exception $e) {
                error_log("Error importing domain {$result['domain']}: " . $e->getMessage());
            }
        }
        
        $_SESSION['success'] = true;
        $_SESSION['message'] = "Successfully imported {$importedCount} domains to reports.";
    }

    private function addToReports($result) {
        $stmt = $this->db->prepare(
            "INSERT INTO domain_reports (domain, reported_by, reason, report_date) 
             VALUES (?, ?, ?, NOW()) 
             ON DUPLICATE KEY UPDATE reason = VALUES(reason), report_date = NOW()"
        );
        
        $reason = "Imported via bulk scan - Status: " . ucfirst($result['status']);
        if (isset($result['scan_data']['expert_analysis'])) {
            $reason .= " - " . $result['scan_data']['expert_analysis'];
        }
        
        $stmt->execute([
            $result['domain'],
            $_SESSION['user_id'] ?? 1,
            $reason
        ]);
    }

    private function addToBlacklist($result) {
        $stmt = $this->db->prepare(
            "INSERT INTO domain_blacklist (domain, reason, added_by, added_at) 
             VALUES (?, ?, ?, NOW()) 
             ON DUPLICATE KEY UPDATE reason = VALUES(reason)"
        );
        
        $reason = "Auto-blacklisted via bulk scan - Phishing detected by R1 model";
        if (isset($result['scan_data']['expert_analysis'])) {
            $reason .= " - " . $result['scan_data']['expert_analysis'];
        }
        
        $stmt->execute([
            $result['domain'],
            $reason,
            $_SESSION['user_id'] ?? 1
        ]);
    }

    // Legacy method for file upload (kept for backward compatibility)
    public function importDomains($file) {
        try {
            // Open the CSV file
            $handle = fopen($file, "r");
            if ($handle === false) {
                throw new \Exception("Could not open file");
            }

            // Skip header row
            fgetcsv($handle);

            // Initialize counters
            $count = 0;
            $batch = [];

            // Prepare statements for both tables
            $reportStmt = $this->db->prepare(
                "INSERT INTO domain_reports (domain, reported_by, reason, report_date) 
                 VALUES (?, ?, ?, NOW())"
            );

            $blacklistStmt = $this->db->prepare(
                "INSERT INTO domain_blacklist (domain, reason, added_by, added_at) 
                 VALUES (?, ?, ?, NOW()) 
                 ON DUPLICATE KEY UPDATE reason = VALUES(reason)"
            );

            // Start transaction
            $this->db->beginTransaction();

            // Read CSV line by line
            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 2) {
                    $domain = trim($row[0]);  // Domain is in first column
                    $type = strtolower(trim($row[1])); // Type is in second column

                    // Convert 'legit' to 'safe'
                    if ($type === 'legit') {
                        $type = 'safe';
                    }

                    if (!empty($domain) && in_array($type, ['phishing', 'safe'])) {
                        // Extract domain from URL if needed
                        if (preg_match("/^https?:\/\//i", $domain)) {
                            $domain = parse_url($domain, PHP_URL_HOST);
                        }
                        
                        // Remove www. prefix if present
                        $domain = preg_replace('/^www\./i', '', $domain);
                        
                        // Use R1 model to scan the domain for verification
                        $url = "https://" . $domain;
                        $scanResult = $this->urlScan->scanURL($url, $_SESSION['user_id'] ?? 1, false); // Don't save to history during import
                        
                        // Use R1 model result if available, otherwise use CSV type
                        $finalType = 'safe';
                        if ($scanResult && isset($scanResult['is_phishing'])) {
                            $finalType = $scanResult['is_phishing'] ? 'phishing' : 'safe';
                        } else {
                            $finalType = $type; // Fallback to CSV type if R1 scan fails
                        }
                        
                        // Add to batch
                        $batch[] = [
                            'domain' => $domain,
                            'type' => $finalType,
                            'user_id' => $_SESSION['user_id'] ?? 1,
                            'reason' => $finalType === 'phishing' ? 'Imported phishing domain (verified by R1 model)' : 'Imported safe domain (verified by R1 model)'
                        ];
                        
                        // Process batch if it reaches the batch size
                        if (count($batch) >= 100) {
                            $this->processBatch($batch, $reportStmt, $blacklistStmt);
                            $count += count($batch);
                            $batch = [];
                            
                            // Commit after each batch
                            $this->db->commit();
                            $this->db->beginTransaction();
                        }
                    }
                }
            }

            // Process remaining records
            if (!empty($batch)) {
                $this->processBatch($batch, $reportStmt, $blacklistStmt);
                $count += count($batch);
            }

            fclose($handle);
            $this->db->commit();

            return [
                'success' => true, 
                'message' => "Successfully imported {$count} domains using R1 model verification"
            ];
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Import error: " . $e->getMessage());
            return [
                'success' => false, 
                'message' => "Import failed: " . $e->getMessage()
            ];
        }
    }

    private function processBatch($batch, $reportStmt, $blacklistStmt) {
        foreach ($batch as $record) {
            try {
                // First add to domain_reports
                $reportStmt->execute([
                    $record['domain'],
                    $record['user_id'],
                    $record['reason']
                ]);

                // Then add to domain_blacklist
                $blacklistStmt->execute([
                    $record['domain'],
                    $record['reason'],
                    $record['user_id']
                ]);
            } catch (\PDOException $e) {
                // Log the error but continue with other domains
                error_log("Error importing domain {$record['domain']}: " . $e->getMessage());
                continue;
            }
        }
    }

    private function scanWithML($url) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:5000/predict');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['url' => $url]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                error_log("ML API cURL error: " . $curlError);
                return [
                    'error' => 'ML API connection failed: ' . $curlError,
                    'status' => 'error'
                ];
            }
            
            if ($httpCode !== 200) {
                error_log("ML API HTTP error: " . $httpCode . " - Response: " . $response);
                return [
                    'error' => 'ML API error: HTTP ' . $httpCode,
                    'status' => 'error'
                ];
            }
            
            $result = json_decode($response, true);
            if (!$result || !isset($result['status']) || $result['status'] !== 'success') {
                error_log("ML API invalid response: " . $response);
                return [
                    'error' => 'ML API invalid response',
                    'status' => 'error'
                ];
            }
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("ML API call failed: " . $e->getMessage());
            return [
                'error' => 'ML API call failed: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    private function getWhoisFromR1($url) {
        try {
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) return [];
            
            // Get WHOIS data using the existing R1 system
            $scanResult = $this->urlScan->scanURL($url, null, false, true); // testMode = true to skip domain reachability check
            
            if ($scanResult && isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])) {
                return $scanResult['whois_info'];
            }
            
            return [];
        } catch (\Exception $e) {
            error_log("Error getting WHOIS data: " . $e->getMessage());
            return [];
        }
    }

    private function autoAddToBlacklist($domain) {
        try {
            error_log("Auto-blacklist: Attempting to add domain '$domain' to blacklist");
            
            // Check if domain is already blacklisted
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM domain_blacklist WHERE domain = ?");
            $stmt->execute([$domain]);
            if ($stmt->fetchColumn() > 0) {
                error_log("Auto-blacklist: Domain '$domain' is already blacklisted");
                return true; // Already blacklisted, consider it successful
            }
            
            // Add domain to blacklist
            $userId = $_SESSION['user_id'] ?? 1; // Default to admin user ID 1 if not set
            $reason = 'Auto-detected as phishing by ML model during import';
            
            $stmt = $this->db->prepare("INSERT INTO domain_blacklist (domain, reason, added_by) VALUES (?, ?, ?)");
            $result = $stmt->execute([$domain, $reason, $userId]);
            
            if ($result) {
                error_log("Auto-blacklist: Successfully added domain '$domain' to blacklist");
                return true;
            } else {
                error_log("Auto-blacklist: Failed to add domain '$domain' to blacklist");
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Error auto-adding to blacklist: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Get domain ID from domain name for blacklist operations
    private function getDomainIdFromName($domain) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM domain_blacklist WHERE domain = ?");
            $stmt->execute([$domain]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        } catch (\Exception $e) {
            error_log("Error getting domain ID: " . $e->getMessage());
            return null;
        }
    }

    // Restore domain from blacklist (Back to Safe functionality)
    public function restoreDomain($domain) {
        try {
            error_log("Restore domain: Attempting to restore domain '$domain' from blacklist");
            
            // Get domain ID from domain name
            $domainId = $this->getDomainIdFromName($domain);
            if (!$domainId) {
                error_log("Restore domain: Domain '$domain' not found in blacklist");
                return [
                    'success' => false,
                    'message' => 'Domain not found in blacklist.'
                ];
            }
            
            // Check if domain exists in blacklist
            $stmt = $this->db->prepare("SELECT * FROM domain_blacklist WHERE id = ?");
            $stmt->execute([$domainId]);
            $domainInfo = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$domainInfo) {
                return [
                    'success' => false,
                    'message' => 'Domain not found in blacklist.'
                ];
            }
            
            // Begin transaction
            $this->db->beginTransaction();
            
            try {
                // Remove from domain_blacklist
                $stmt = $this->db->prepare("DELETE FROM domain_blacklist WHERE id = ?");
                $deleteResult = $stmt->execute([$domainId]);
                
                if (!$deleteResult) {
                    throw new \Exception("Failed to delete from blacklist");
                }
                
                // Update scanned_domains table
                $stmt = $this->db->prepare("UPDATE scanned_domains SET is_blacklisted = 0, blacklist_date = NULL, blacklist_reason = NULL WHERE domain = ?");
                $updateResult = $stmt->execute([$domain]);
                
                // Add to safe_domains table
                $stmt = $this->db->prepare("INSERT IGNORE INTO safe_domains (domain, restored_by, reason) VALUES (?, ?, ?)");
                $safeResult = $stmt->execute([$domain, $_SESSION['user_id'] ?? 1, 'Restored from blacklist via import page']);
                
                $this->db->commit();
                
                error_log("Restore domain: Successfully restored domain '$domain' from blacklist");
                
                return [
                    'success' => true,
                    'message' => "Domain '{$domain}' restored as safe and removed from blacklist successfully."
                ];
                
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
            
        } catch (\Exception $e) {
            error_log("Error restoring domain: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => 'Failed to restore domain from blacklist: ' . $e->getMessage()
            ];
        }
    }

    // Save import history for tracking
    private function saveImportHistory($domain, $scanResult, $userId) {
        try {
            // Check if import history already exists for this domain
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM import_history WHERE domain = ?");
            $stmt->execute([$domain]);
            if ($stmt->fetchColumn() > 0) {
                // Update existing import history
                $stmt = $this->db->prepare("
                    UPDATE import_history 
                    SET 
                        import_count = import_count + 1,
                        last_import_date = NOW(),
                        last_scan_result = ?,
                        last_ml_prediction = ?,
                        last_confidence_score = ?,
                        last_auto_blacklisted = ?
                    WHERE domain = ?
                ");
                $stmt->execute([
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['confidence_score'] ?? 0,
                    $scanResult['auto_blacklisted'] ?? false ? 1 : 0,
                    $domain
                ]);
            } else {
                // Create new import history
                $stmt = $this->db->prepare("
                    INSERT INTO import_history (
                        domain, 
                        user_id, 
                        first_import_date, 
                        last_import_date, 
                        import_count, 
                        first_scan_result, 
                        last_scan_result, 
                        first_ml_prediction, 
                        last_ml_prediction, 
                        first_confidence_score, 
                        last_confidence_score, 
                        first_auto_blacklisted, 
                        last_auto_blacklisted
                    ) VALUES (?, ?, NOW(), NOW(), 1, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $domain,
                    $userId,
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['prediction'] ?? 'unknown',
                    $scanResult['confidence_score'] ?? 0,
                    $scanResult['confidence_score'] ?? 0,
                    $scanResult['auto_blacklisted'] ?? false ? 1 : 0,
                    $scanResult['auto_blacklisted'] ?? false ? 1 : 0
                ]);
            }
            
            error_log("Import history saved for domain: $domain");
            return true;
            
        } catch (\Exception $e) {
            error_log("Error saving import history: " . $e->getMessage());
            return false;
        }
    }
} 