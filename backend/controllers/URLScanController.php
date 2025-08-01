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
            
            // Perform the scan
            $scanResult = $this->urlScan->scanURL($url, $userId);
            
            return $scanResult;
            
        } catch (\Exception $e) {
            // Log error
            error_log("URL Scan Error: " . $e->getMessage());
            return null;
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
} 