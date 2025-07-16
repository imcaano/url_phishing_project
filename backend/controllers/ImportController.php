<?php
namespace App\Controllers;

use App\Config\Database;
use PDO;

class ImportController {
    private $db;
    private $batchSize = 100; // Process 100 records at a time

    public function __construct() {
        $this->db = Database::getDB();
        // Increase PHP execution time limit
        set_time_limit(300); // 5 minutes
    }

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
                        
                        // Add to batch
                        $batch[] = [
                            'domain' => $domain,
                            'type' => $type,
                            'user_id' => $_SESSION['user_id'] ?? 1,
                            'reason' => $type === 'phishing' ? 'Imported phishing domain' : 'Imported safe domain'
                        ];
                        
                        // Process batch if it reaches the batch size
                        if (count($batch) >= $this->batchSize) {
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
                'message' => "Successfully imported {$count} domains"
            ];

        } catch (\Exception $e) {
            if (isset($handle) && $handle !== false) {
                fclose($handle);
            }
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return [
                'success' => false, 
                'message' => 'Error importing domains: ' . $e->getMessage()
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

    public function index() {
        // Get recent imports
        $recentImports = $this->getRecentImports();
        // Display the import form
        require_once __DIR__ . '/../views/admin/import.php';
    }

    private function getRecentImports($limit = 10) {
        try {
            $query = "SELECT d.domain, d.reason as type, d.added_at, u.username 
                     FROM domain_blacklist d 
                     LEFT JOIN users u ON d.added_by = u.id 
                     ORDER BY d.added_at DESC 
                     LIMIT ?";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error fetching recent imports: " . $e->getMessage());
            return [];
        }
    }

    public function importFromExcel() {
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Please upload a valid CSV file'];
        }

        // Check file type
        $mimeType = mime_content_type($_FILES['file']['tmp_name']);
        if (!in_array($mimeType, ['text/csv', 'text/plain', 'application/vnd.ms-excel'])) {
            return ['success' => false, 'message' => 'Please upload a CSV file only'];
        }

        try {
            // Import domains from CSV
            $result = $this->importDomains($_FILES['file']['tmp_name']);
            
            if ($result['success']) {
                $_SESSION['success'] = true;
                $_SESSION['message'] = $result['message'];
            } else {
                $_SESSION['success'] = false;
                $_SESSION['message'] = $result['message'];
            }
            
            // Redirect back to import page
            header('Location: /url_phishing_project/public/import');
            exit;
            
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return ['success' => false, 'message' => 'Error during import: ' . $e->getMessage()];
        }
    }
} 