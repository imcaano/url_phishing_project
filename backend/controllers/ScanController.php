<?php
namespace App\Controllers;

use App\Models\URLScan;

class ScanController {
    private $urlScan;

    public function __construct() {
        $this->urlScan = new URLScan();
    }

    public function index() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $url = $_POST['url'] ?? '';
                $userId = $_SESSION['user_id'] ?? null;
                $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
                
                if (!empty($url)) {
                    // Use R1 model for scanning - it already includes WHOIS and phishing detection
                    $scanResult = $this->urlScan->scanURL($url, $userId, $isAdmin);
                    
                    if ($scanResult === false) {
                        $error = "Failed to scan URL. Please try again.";
                    } else {
                        // Store scan result in session for display
                        $_SESSION['scan_result'] = $scanResult;
                    }
                    // scanResult already contains all necessary data from R1 model
                } else {
                    $error = "Please enter a valid URL.";
                }
            }
            
            // Display scan result if available
            $scanResult = $_SESSION['scan_result'] ?? null;
            if ($scanResult) {
                unset($_SESSION['scan_result']); // Clear after display
            }
            
            require_once __DIR__ . '/../views/scan.php';
        } catch (\Exception $e) {
            error_log("Error in user scan: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while scanning the URL.";
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
    }

    public function scanUrl($url) {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return [
                    'success' => false,
                    'message' => 'Invalid URL format'
                ];
            }

            // Add http:// if no protocol specified
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }

            // Get user ID from session
            $userId = $_SESSION['user_id'] ?? null;
            $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
            
            // Use R1 model for scanning - it already includes WHOIS and phishing detection
            $scanResult = $this->urlScan->scanURL($url, $userId, $isAdmin);
            
            if ($scanResult === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to scan URL. Please try again.'
                ];
            }

            // scanResult already contains all necessary data from R1 model
            return [
                'success' => true,
                'message' => 'Scan completed successfully',
                'scan_result' => $scanResult
            ];

        } catch (\Exception $e) {
            error_log("Error in scanUrl: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while scanning the URL.'
            ];
        }
    }

    public function displayScanResult() {
        $scanResult = $_SESSION['scan_result'] ?? null;
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        
        if ($isAdmin) {
            include '../views/admin/scan.php';
        } else {
            include '../views/scan.php';
        }
        
        // Clear scan result from session
        unset($_SESSION['scan_result']);
    }
} 