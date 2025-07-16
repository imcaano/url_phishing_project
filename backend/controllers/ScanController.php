<?php
namespace App\Controllers;

use App\Models\URLScan;
use App\Models\DomainInfo;

class ScanController {
    private $urlScan;

    public function __construct() {
        $this->urlScan = new URLScan();
    }

    public function index() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $url = $_POST['url'] ?? '';
                if (!empty($url)) {
                    $scanResult = $this->urlScan->scanURL($url, $_SESSION['user_id']);
                    
                    if ($scanResult === false) {
                        $error = "Failed to scan URL. Please try again.";
                    } else {
                        // Get WHOIS information
                        $domainInfo = new DomainInfo();
                        $whoisData = $domainInfo->getDomainInfo($url);
                        
                        if ($whoisData) {
                            $scanResult['whois'] = [
                                'Domain Age' => $whoisData['domain_age'] ?? 'Unknown',
                                'Domain Status' => is_array($whoisData['status']) ? implode(', ', $whoisData['status']) : ($whoisData['status'] ?? 'Unknown'),
                                'Domain Registrar' => $whoisData['registrar'] ?? 'Unknown',
                                'Domain Expiry' => $whoisData['expiration_date'] ?? 'Unknown',
                                'Last Updated' => $whoisData['last_updated'] ?? 'Unknown',
                                'Nameservers' => is_array($whoisData['nameservers']) ? implode(', ', $whoisData['nameservers']) : ($whoisData['nameservers'] ?? 'Unknown')
                            ];
                        }
                    }
                } else {
                    $error = "Please enter a valid URL.";
                }
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
            
            // Perform the scan
            $scanResult = $this->urlScan->scanURL($url, $userId);
            
            if ($scanResult === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to scan URL. Please try again.'
                ];
            }

            // Get WHOIS information
            $domainInfo = new DomainInfo();
            $whoisData = $domainInfo->getDomainInfo($url);
            
            if ($whoisData) {
                $scanResult['whois'] = [
                    'Domain Age' => $whoisData['domain_age'] ?? 'Unknown',
                    'Domain Status' => is_array($whoisData['status']) ? implode(', ', $whoisData['status']) : ($whoisData['status'] ?? 'Unknown'),
                    'Domain Registrar' => $whoisData['registrar'] ?? 'Unknown',
                    'Domain Expiry' => $whoisData['expiration_date'] ?? 'Unknown',
                    'Last Updated' => $whoisData['last_updated'] ?? 'Unknown',
                    'Nameservers' => is_array($whoisData['nameservers']) ? implode(', ', $whoisData['nameservers']) : ($whoisData['nameservers'] ?? 'Unknown')
                ];
            }

            return [
                'success' => true,
                'message' => 'Scan completed successfully',
                'scan_result' => $scanResult
            ];

        } catch (\Exception $e) {
            error_log("Error scanning URL: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while scanning the URL'
            ];
        }
    }
} 