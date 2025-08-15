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
                    // Use ML model for scanning
                    $scanResult = $this->scanWithML($url);
                    
                    if ($scanResult['status'] === 'success') {
                        // Get WHOIS data from R1 system
                        $whoisData = $this->getWhoisFromR1($scanResult['url']);
                        if (!empty($whoisData)) {
                            $scanResult['whois_info'] = $whoisData;
                        } else {
                            $scanResult['whois_info'] = [];
                        }
                        
                        // Add user ID and scan date for database storage
                        $scanResult['user_id'] = $_SESSION['user_id'] ?? null;
                        $scanResult['scan_date'] = date('Y-m-d H:i:s');
                        
                        // Save scan result to database for reports
                        $this->urlScan->saveScanResult($scanResult, $_SESSION['user_id'] ?? null);
                        
                        // Auto-add phishing domains to blacklist
                        if (isset($scanResult['prediction']) && $scanResult['prediction'] === 'phishing') {
                            $this->autoAddToBlacklist($scanResult['url']);
                        }
                        
                        // Store scan result in session for display
                        $_SESSION['scan_result'] = $scanResult;
                    } else {
                        $error = $scanResult['error'] ?? "Failed to scan URL. Please try again.";
                    }
                } else {
                    $error = "Please enter a valid URL.";
                }
            }
            
            // Display scan result if available
            $scanResult = $_SESSION['scan_result'] ?? null;
            if ($scanResult) {
                unset($_SESSION['scan_result']); // Clear after display
            }
            
            // Pass error to view
            $viewError = $error ?? null;
            
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

    public function scanWithML($url) {
        try {
            // First, validate URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return [
                    'error' => 'Invalid URL format. Please enter a valid URL.',
                    'status' => 'error'
                ];
            }
            
            // Extract domain from URL
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) {
                return [
                    'error' => 'Invalid domain format',
                    'status' => 'error'
                ];
            }
            
            // Use R1 system to check if domain is reachable and valid
            $r1Validation = $this->urlScan->scanURL($url, null, false, true); // testMode = true for validation only
            
            if (!$r1Validation || $r1Validation['status'] === 'not_found' || $r1Validation['status'] === 'error' || $r1Validation['status'] === 'suspicious') {
                $errorMessage = $r1Validation['error'] ?? 'Domain not found or unreachable. Please check the URL and try again.';
                return [
                    'error' => $errorMessage,
                    'status' => 'error'
                ];
            }
            
            // Domain is valid and reachable, now call the ML API
            $apiUrl = 'http://localhost:5000/predict';
            $postData = json_encode(['url' => $url]);
            
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
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
            
            // Return the ML API result
            return $result;
            
        } catch (\Exception $e) {
            error_log("ML API call failed: " . $e->getMessage());
            return [
                'error' => 'ML API call failed: ' . $e->getMessage(),
                'status' => 'error'
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
    
    private function getWhoisFromR1($url) {
        try {
            // Use the existing URLScan model to get WHOIS data
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) return [];
            
            // Get WHOIS data using the existing R1 system by calling scanURL
            // This will give us the complete scan result including WHOIS data
            $scanResult = $this->urlScan->scanURL($url, null, false, true); // testMode = true to skip domain reachability check
            
            if ($scanResult && isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])) {
                return $scanResult['whois_info'];
            }
            
            return [];
            
        } catch (\Exception $e) {
            error_log("Error getting WHOIS data from R1: " . $e->getMessage());
            return [];
        }
    }
    
    private function autoAddToBlacklist($url) {
        try {
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) return false;
            
            // Call admin blacklist API to add domain
            $apiUrl = 'http://localhost' . '/url_phishing_project/public/admin/blacklist';
            $postData = http_build_query([
                'action' => 'add',
                'domain' => $domain,
                'reason' => 'Auto-detected as phishing by ML model'
            ]);
            
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                error_log("Auto-added phishing domain to blacklist: " . $domain);
                return true;
            } else {
                error_log("Failed to auto-add domain to blacklist: " . $domain . " - HTTP " . $httpCode);
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Error auto-adding to blacklist: " . $e->getMessage());
            return false;
        }
    }
} 