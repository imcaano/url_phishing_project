<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class URLScan {
    private $db;
    
    public function __construct() {
        $this->db = Database::getDB();
    }
    
    public function scanURL($url, $userId = null) {
        try {
            // Validate URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                throw new \Exception("Invalid URL format");
            }

            // Extract domain from URL
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) {
                throw new \Exception("Could not extract domain from URL");
            }
            
            // Remove www. prefix if present
            $domain = preg_replace('/^www\./i', '', $domain);

            // Check if domain is in blacklist
            $stmt = $this->db->prepare(
                "SELECT * FROM domain_blacklist WHERE domain = ?"
            );
            $stmt->execute([$domain]);
            $blacklistResult = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($blacklistResult) {
                // If domain is in blacklist, return high risk result
                $result = [
                    'url' => $url,
                    'is_phishing' => true,
                    'confidence_score' => 100,
                    'risk_level' => 'HIGH',
                    'features' => [
                        'Blacklist Status' => 'Domain is blacklisted',
                        'Reason' => $blacklistResult['reason']
                    ]
                ];
            } else {
                // Call Python API for prediction
                $ch = curl_init('http://127.0.0.1:5000/predict');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['url' => $url]));
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                if ($httpCode === 200) {
                    $prediction = json_decode($response, true);
                    $result = [
                        'url' => $url,
                        'is_phishing' => $prediction['is_phishing'],
                        'confidence_score' => $prediction['confidence_score'],
                        'risk_level' => $prediction['confidence_score'] >= 75 ? 'HIGH' : 
                                      ($prediction['confidence_score'] >= 50 ? 'MEDIUM' : 'LOW'),
                        'features' => $prediction['features']
                    ];
                } else {
                    // Fallback to basic feature extraction if API fails
                    $features = $this->extractFeatures($url);
                    $confidenceScore = $this->calculateRiskScore($features);
                    $isPhishing = $confidenceScore >= 75;
                    
                    $result = [
                        'url' => $url,
                        'is_phishing' => $isPhishing,
                        'confidence_score' => $confidenceScore,
                        'risk_level' => $confidenceScore >= 75 ? 'HIGH' : 
                                      ($confidenceScore >= 50 ? 'MEDIUM' : 'LOW'),
                        'features' => $features
                    ];
                }
            }
            
            // Save scan to database
            $stmt = $this->db->prepare(
                "INSERT INTO url_scans (url, user_id, is_phishing, confidence_score, scan_features) 
                 VALUES (?, ?, ?, ?, ?)"
            );
            
            $stmt->execute([
                $url, 
                $userId, 
                $result['is_phishing'] ? 1 : 0, 
                $result['confidence_score'],
                json_encode($result['features'])
            ]);
            
            return $result;

        } catch (\Exception $e) {
            error_log("URL Scan Error: " . $e->getMessage());
            return false;
        }
    }
    
    public function getRecentScans($userId, $limit = 5) {
        $stmt = $this->db->prepare(
            "SELECT * FROM url_scans WHERE user_id = ? ORDER BY scan_date DESC LIMIT ?"
        );
        $stmt->execute([$userId, $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalScans() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM url_scans");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting total scans: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPhishingCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM url_scans WHERE is_phishing = 1");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting phishing count: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getTodayAPIRequests() {
        $stmt = $this->db->query(
            "SELECT SUM(requests_today) as total FROM api_keys"
        );
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    public function predictPhishing($url) {
        // Extract features from URL
        $features = $this->extractFeatures($url);
        
        // Perform prediction
        $prediction = $this->runPredictionModel($features);
        
        return [
            'is_phishing' => $prediction['is_phishing'],
            'confidence_score' => $prediction['confidence'] * 100
        ];
    }

    private function extractFeatures($url) {
        $features = [];
        
        // Parse URL
        $parsedUrl = parse_url($url);
        $domain = $parsedUrl['host'] ?? '';
        $path = $parsedUrl['path'] ?? '';
        $query = $parsedUrl['query'] ?? '';
        
        // Basic URL features
        $features['URL Length'] = strlen($url);
        $features['Domain Length'] = strlen($domain);
        $features['Path Length'] = strlen($path);
        $features['Query Length'] = strlen($query);
        $features['Dots in Domain'] = substr_count($domain, '.');
        $features['Contains IP Address'] = preg_match('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $domain) ? 'Yes' : 'No';
        $features['Contains @ Symbol'] = strpos($url, '@') !== false ? 'Yes' : 'No';
        $features['Uses HTTPS'] = (parse_url($url, PHP_URL_SCHEME) === 'https') ? 'Yes' : 'No';
        
        // Advanced URL features
        $features['Has Multiple Subdomains'] = (substr_count($domain, '.') > 2) ? 'Yes' : 'No';
        $features['Contains Hexadecimal'] = (preg_match('/[0-9a-f]{8,}/i', $url)) ? 'Yes' : 'No';
        $features['Contains Numbers in Domain'] = (preg_match('/\d/', $domain)) ? 'Yes' : 'No';
        $features['Contains Special Chars'] = (preg_match('/[^a-zA-Z0-9\-\._~:\/?#\[\]@!$&\'()*+,;=]/', $url)) ? 'Yes' : 'No';
        
        // Random or suspicious domain patterns
        $features['Contains Random String'] = (preg_match('/[a-z0-9]{10,}/', $domain)) ? 'Yes' : 'No';
        $features['Has Suspicious TLD'] = $this->hasSuspiciousTLD($domain) ? 'Yes' : 'No';
        
        // Brand protection checks
        $popularBrands = [
            'paypal', 'apple', 'amazon', 'microsoft', 'google', 'facebook', 'netflix', 
            'bank', 'wellsfargo', 'chase', 'citibank', 'hsbc', 'barclays', 'santander',
            'binance', 'coinbase', 'blockchain', 'crypto', 'wallet', 'instagram', 'tiktok',
            'whatsapp', 'twitter', 'linkedin', 'outlook', 'office365', 'onedrive'
        ];
        
        $brandCount = 0;
        foreach ($popularBrands as $brand) {
            if (stripos($domain, $brand) !== false) {
                $brandCount++;
            }
        }
        $features['Contains Brand Names'] = $brandCount;
        
        // Suspicious keywords
        $suspiciousWords = [
            'login', 'signin', 'account', 'password', 'secure', 'update', 'verify',
            'confirm', 'banking', 'security', 'authenticate', 'wallet', 'recover',
            'suspended', 'unusual', 'unauthorized', 'limited', 'alert', 'warning',
            'important', 'access', 'restriction', 'blocked', 'verify', 'validation',
            'authenticate', 'restore', 'billing', 'payment', 'subscription'
        ];
        
        $suspiciousCount = 0;
        foreach ($suspiciousWords as $word) {
            if (stripos($url, $word) !== false) {
                $suspiciousCount++;
            }
        }
        $features['Suspicious Words'] = $suspiciousCount;
        
        // Domain age and registration (from WHOIS)
        $domainInfo = new DomainInfo();
        $whoisData = $domainInfo->getDomainInfo($url);
        
        if ($whoisData && isset($whoisData['is_registered'])) {
            $features['Domain Age'] = $whoisData['domain_age'];
            $features['Domain Registered'] = $whoisData['is_registered'] ? 'Yes' : 'No';
            $features['Domain Expiry'] = $whoisData['expiration_date'];
            $features['Domain Status'] = is_array($whoisData['status']) 
                ? implode(', ', $whoisData['status']) 
                : $whoisData['status'];
            $features['Domain Registrar'] = $whoisData['registrar'];
            $features['Last Updated'] = $whoisData['last_updated'];
            $features['Domain Owner'] = $whoisData['owner'];
            $features['WHOIS Server'] = $whoisData['whois_server'];
            $features['Nameservers'] = is_array($whoisData['nameservers']) 
                ? implode(', ', $whoisData['nameservers']) 
                : $whoisData['nameservers'];
            $features['Domain ID'] = $whoisData['domain_id'];
            $features['Status Description'] = $whoisData['status_details']['description'];
        } else {
            $features['Domain Age'] = 'Unknown';
            $features['Domain Registered'] = 'Unknown';
            $features['Domain Expiry'] = 'Unknown';
            $features['Domain Status'] = 'Status information unavailable';
            $features['Domain Registrar'] = 'Unknown';
            $features['Last Updated'] = 'Unknown';
            $features['Domain Owner'] = 'Unknown';
            $features['WHOIS Server'] = 'Unknown';
            $features['Nameservers'] = 'Unknown';
            $features['Domain ID'] = 'Unknown';
            $features['Status Description'] = 'Unknown';
        }
        
        return $features;
    }

    private function hasSuspiciousTLD($domain) {
        $suspiciousTLDs = [
            '.xyz', '.top', '.work', '.live', '.click', '.loan', '.review',
            '.bid', '.party', '.trade', '.date', '.racing', '.download',
            '.stream', '.win', '.fit', '.gq', '.ml', '.cf', '.ga', '.tk'
        ];
        
        foreach ($suspiciousTLDs as $tld) {
            if (stripos($domain, $tld) !== false) {
                return true;
            }
        }
        return false;
    }

    public function calculateRiskScore($features) {
        $score = 0;
        $maxScore = 0;  // Track maximum possible score
        
        // URL structure (40% weight)
        if ($features['URL Length'] > 50) { $score += 0.2; $maxScore += 0.2; }
        if ($features['Domain Length'] > 20) { $score += 0.1; $maxScore += 0.1; }
        if ($features['Dots in Domain'] > 2) { $score += 0.2; $maxScore += 0.2; }
        if ($features['Contains IP Address'] === 'Yes') { $score += 0.4; $maxScore += 0.4; }
        if ($features['Contains @ Symbol'] === 'Yes') { $score += 0.4; $maxScore += 0.4; }
        if ($features['Has Multiple Subdomains'] === 'Yes') { $score += 0.2; $maxScore += 0.2; }
        if ($features['Contains Hexadecimal'] === 'Yes') { $score += 0.3; $maxScore += 0.3; }
        if ($features['Contains Special Chars'] === 'Yes') { $score += 0.2; $maxScore += 0.2; }
        if ($features['Uses HTTPS'] === 'No') { $score += 0.1; $maxScore += 0.1; }
        if ($features['Contains Numbers in Domain'] === 'Yes') { $score += 0.2; $maxScore += 0.2; }
        if ($features['Contains Random String'] === 'Yes') { $score += 0.3; $maxScore += 0.3; }
        if ($features['Has Suspicious TLD'] === 'Yes') { $score += 0.3; $maxScore += 0.3; }
        
        // Brand impersonation (30% weight)
        if ($features['Contains Brand Names'] > 0) { $score += 0.3; $maxScore += 0.3; }
        
        // Suspicious keywords (30% weight)
        if ($features['Suspicious Words'] > 0) { $score += 0.3; $maxScore += 0.3; }
        
        // Calculate final score as percentage
        return ($maxScore > 0) ? round(($score / $maxScore) * 100, 2) : 0;
    }

    private function runPredictionModel($features) {
        // Simple rule-based prediction (replace with machine learning model)
        $score = 0;
        
        if ($features['URL Length'] > 100) $score += 0.3;
        if ($features['Dots in Domain'] > 3) $score += 0.2;
        if ($features['Contains IP Address'] === 'Yes') $score += 0.4;
        if ($features['Contains @ Symbol'] === 'Yes') $score += 0.4;
        if ($features['URL Depth'] > 4) $score += 0.2;
        if ($features['Suspicious Words'] > 2) $score += 0.3;
        
        return [
            'is_phishing' => $score > 0.6,
            'confidence' => $score
        ];
    }

    public function getReports($filters = []) {
        try {
            // Ensure user_id is provided and not empty
            if (empty($filters['user_id'])) {
                return [];  // Return empty if no user_id provided
            }

            $query = "SELECT 
                id,
                url,
                is_phishing,
                confidence_score,
                scan_date,
                CASE 
                    WHEN confidence_score >= 75 THEN 'HIGH'
                    WHEN confidence_score >= 50 THEN 'MEDIUM'
                    ELSE 'LOW'
                END as risk_level
            FROM url_scans 
            WHERE user_id = ?";  // Always filter by user_id
            
            $params = [$filters['user_id']];

            // Add date filter if provided
        if (!empty($filters['date_from'])) {
                $query .= " AND DATE(scan_date) >= ?";
            $params[] = $filters['date_from'];
            }

            // Add status filter if provided
            if (!empty($filters['status'])) {
                $query .= " AND is_phishing = ?";
                $params[] = ($filters['status'] === 'phishing') ? 1 : 0;
            }

            $query .= " ORDER BY scan_date DESC";

            $stmt = $this->db->prepare($query);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error in getReports: " . $e->getMessage());
            return [];
        }
    }

    public function getUserTotalScans($userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM url_scans WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting total scans: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getUserPhishingScans($userId) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM url_scans WHERE user_id = ? AND is_phishing = 1");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting phishing scans: " . $e->getMessage());
            return 0;
        }
    }

    public function getAllScans() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    us.*,
                    u.username
                FROM url_scans us
                LEFT JOIN users u ON us.user_id = u.id
                ORDER BY us.scan_date DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting all scans: " . $e->getMessage());
            return [];
        }
    }

    public function countBrandNames($url) {
        $brandNames = [
            'google', 'facebook', 'microsoft', 'apple', 'amazon', 'netflix', 'paypal', 'ebay',
            'twitter', 'instagram', 'linkedin', 'youtube', 'dropbox', 'adobe', 'cisco', 'intel',
            'nvidia', 'amd', 'samsung', 'sony', 'nike', 'adidas', 'coca-cola', 'pepsi',
            'mcdonalds', 'starbucks', 'walmart', 'target', 'costco', 'bestbuy', 'dell', 'hp',
            'lenovo', 'asus', 'acer', 'huawei', 'xiaomi', 'oppo', 'vivo', 'oneplus'
        ];
        
        $count = 0;
        $url = strtolower($url);
        
        foreach ($brandNames as $brand) {
            if (strpos($url, $brand) !== false) {
                $count++;
            }
        }
        
        return $count;
    }

    public function countSuspiciousWords($url) {
        $suspiciousWords = [
            'login', 'signin', 'account', 'verify', 'confirm', 'update', 'secure', 'security',
            'password', 'credential', 'wallet', 'payment', 'bank', 'credit', 'card', 'debit',
            'transaction', 'transfer', 'money', 'fund', 'claim', 'reward', 'prize', 'winner',
            'urgent', 'important', 'action', 'required', 'expired', 'suspended', 'locked',
            'unusual', 'suspicious', 'verify', 'validation', 'authenticate', 'authorize'
        ];
        
        $count = 0;
        $url = strtolower($url);
        
        foreach ($suspiciousWords as $word) {
            if (strpos($url, $word) !== false) {
                $count++;
            }
        }
        
        return $count;
    }

    public function getUserSafeScans($userId) {
        try {
            $query = "SELECT COUNT(*) FROM url_scans WHERE user_id = :user_id AND is_phishing = 0";
            $stmt = $this->db->prepare($query);
            $stmt->execute(['user_id' => $userId]);
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            error_log("Error getting user safe scans: " . $e->getMessage());
            return 0;
        }
    }

    public function getFilteredScans($filters = []) {
        $query = "SELECT us.*, u.username FROM url_scans us LEFT JOIN users u ON us.user_id = u.id WHERE 1=1";
        $params = [];
        if (!empty($filters['date_from'])) {
            $query .= " AND DATE(us.scan_date) >= ?";
            $params[] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $query .= " AND DATE(us.scan_date) <= ?";
            $params[] = $filters['date_to'];
        }
        if (isset($filters['is_phishing']) && $filters['is_phishing'] !== '') {
            $query .= " AND us.is_phishing = ?";
            $params[] = $filters['is_phishing'];
        }
        $query .= " ORDER BY us.scan_date DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScanById($id) {
        $stmt = $this->db->prepare("SELECT us.*, u.username FROM url_scans us LEFT JOIN users u ON us.user_id = u.id WHERE us.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
} 