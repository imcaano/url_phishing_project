<?php
namespace App\Controllers;

use App\Models\URLScan;
use App\Models\DomainInfo;

class URLScanController {
    private $urlScan;
    private $domainInfo;
    
    public function __construct() {
        $this->urlScan = new URLScan();
        $this->domainInfo = new DomainInfo();
    }
    
    public function scan() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return null;
        }
        
        $url = $_POST['url'] ?? '';
        if (empty($url)) {
            return null;
        }
        
        // Add http:// if no protocol specified
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "http://" . $url;
        }
        
        try {
            // Get user ID from session
            $userId = $_SESSION['user_id'] ?? null;
            
            // Use ML model for scanning (no R1 fallback)
            $scanResult = $this->scanWithML($url);
            
            if ($scanResult['status'] === 'success') {
                // Add user ID and save to database
                $scanResult['user_id'] = $userId;
                $scanResult['scan_date'] = date('Y-m-d H:i:s');
                
                // Save scan result to database
                $this->urlScan->saveScanResult($scanResult, $userId);
            
            return $scanResult;
            } else {
                // Return error from ML API
                return $scanResult;
            }
            
        } catch (\Exception $e) {
            // Log error
            error_log("URL Scan Error: " . $e->getMessage());
            return [
                'error' => 'Scan failed: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    public function getReports($filters = []) {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                return [];
            }

            // Add user_id to filters
            $filters['user_id'] = $userId;
            
            return $this->urlScan->getReports($filters);

        } catch (\Exception $e) {
            error_log("Error fetching reports: " . $e->getMessage());
            return [];
        }
    }

    public function getScannedDomains($filters = []) {
        try {
            return $this->urlScan->getScannedDomains($filters);
        } catch (\Exception $e) {
            error_log("Error fetching scanned domains: " . $e->getMessage());
            return [];
        }
    }

    public function getScannedDomainStats() {
        try {
            return $this->urlScan->getScannedDomainStats();
        } catch (\Exception $e) {
            error_log("Error fetching scanned domain stats: " . $e->getMessage());
            return [
                'total_domains' => 0,
                'phishing_domains' => 0,
                'blacklisted_domains' => 0,
                'total_scans' => 0,
                'avg_confidence_score' => 0
            ];
        }
    }
    
    public function scanWithML($url) {
        try {
            // Call the ML API directly
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
} 