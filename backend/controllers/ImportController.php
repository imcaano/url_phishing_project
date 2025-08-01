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
            } elseif (isset($_POST['import_all'])) {
                // Handle manual import all to reports
                if (isset($_SESSION['scan_results'])) {
                    $this->importAllToReports($_SESSION['scan_results']);
                    unset($_SESSION['scan_results']);
                }
            }
        }
        
        // Store scan results in session for potential re-import
        if (!empty($scanResults)) {
            $_SESSION['scan_results'] = $scanResults;
        }
        
        // Display the import form
        require_once __DIR__ . '/../views/admin/import.php';
    }

    private function scanDomains($domainsText) {
        $domains = $this->parseDomains($domainsText);
        $results = [];
        
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
                
                // Scan the domain using R1 model
                $scanResult = $this->urlScan->scanURL($url, $_SESSION['user_id'] ?? 1, true); // Save to history
                
                if ($scanResult) {
                    $results[] = [
                        'domain' => $cleanDomain,
                        'status' => $this->determineStatus($scanResult),
                        'confidence' => $scanResult['confidence_score'] ?? 'N/A',
                        'whois' => $scanResult['whois'] ?? [],
                        'scan_data' => $scanResult
                    ];
                } else {
                    $results[] = [
                        'domain' => $cleanDomain,
                        'status' => 'unknown',
                        'confidence' => 'N/A',
                        'whois' => [],
                        'scan_data' => null
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
} 