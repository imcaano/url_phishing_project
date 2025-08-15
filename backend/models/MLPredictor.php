<?php
/**
 * Machine Learning Predictor Integration
 * Connects to the trained ML model via Flask API
 */

namespace App\Models;

class MLPredictor {
    private $apiUrl;
    private $timeout;
    private $maxRetries;
    
    public function __init__() {
        $this->apiUrl = 'http://localhost:5000';
        $this->timeout = 30;
        $this->maxRetries = 3;
    }
    
    /**
     * Set the ML API URL
     */
    public function setApiUrl($url) {
        $this->apiUrl = rtrim($url, '/');
    }
    
    /**
     * Check if ML API is available
     */
    public function isApiAvailable() {
        try {
            $response = $this->makeRequest('/health', 'GET');
            return isset($response['status']) && $response['status'] === 'healthy';
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get model information
     */
    public function getModelInfo() {
        try {
            $response = $this->makeRequest('/model_info', 'GET');
            return $response;
        } catch (Exception $e) {
            return [
                'error' => 'Failed to get model info: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
    
    /**
     * Predict phishing status for a single URL
     */
    public function predictUrl($url) {
        try {
            $data = ['url' => $url];
            $response = $this->makeRequest('/predict', 'POST', $data);
            return $response;
        } catch (Exception $e) {
            return [
                'error' => 'Failed to predict URL: ' . $e->getMessage(),
                'status' => 'error',
                'url' => $url
            ];
        }
    }
    
    /**
     * Predict phishing status for multiple URLs
     */
    public function predictBatch($urls) {
        try {
            if (!is_array($urls)) {
                $urls = [$urls];
            }
            
            // Limit batch size
            if (count($urls) > 100) {
                $urls = array_slice($urls, 0, 100);
            }
            
            $data = ['urls' => $urls];
            $response = $this->makeRequest('/batch_predict', 'POST', $data);
            return $response;
        } catch (Exception $e) {
            return [
                'error' => 'Failed to predict batch: ' . $e->getMessage(),
                'status' => 'error',
                'urls' => $urls
            ];
        }
    }
    
    /**
     * Enhanced prediction with fallback analysis
     */
    public function predictWithFallback($url) {
        // Try ML prediction first
        $mlResult = $this->predictUrl($url);
        
        if ($mlResult['status'] === 'success') {
            return $mlResult;
        }
        
        // Fallback to traditional analysis
        return $this->fallbackAnalysis($url);
    }
    
    /**
     * Traditional feature-based analysis as fallback
     */
    private function fallbackAnalysis($url) {
        $features = $this->extractFeatures($url);
        $riskScore = $this->calculateRiskScore($features);
        $isPhishing = $riskScore > 0.7;
        
        return [
            'url' => $url,
            'prediction' => $isPhishing ? 'phishing' : 'safe',
            'confidence_score' => round($riskScore * 100, 2),
            'risk_level' => $this->getRiskLevel($riskScore),
            'features' => $features,
            'expert_analysis' => $this->createExpertAnalysis($url, $features, $isPhishing, $riskScore),
            'model_info' => [
                'model_type' => 'Traditional Analysis (Fallback)',
                'accuracy' => 'Estimated 75-85%',
                'training_date' => 'N/A'
            ],
            'timestamp' => date('c'),
            'status' => 'success',
            'fallback_used' => true
        ];
    }
    
    /**
     * Extract features from URL for fallback analysis
     */
    private function extractFeatures($url) {
        $parsed = parse_url($url);
        $domain = $parsed['host'] ?? '';
        
        // Basic features
        $urlLength = strlen($url);
        $domainLength = strlen($domain);
        $pathLength = strlen($parsed['path'] ?? '');
        $queryLength = strlen($parsed['query'] ?? '');
        
        // Domain analysis
        $dotsInDomain = substr_count($domain, '.');
        $containsIp = filter_var($domain, FILTER_VALIDATE_IP);
        $containsAt = strpos($url, '@') !== false;
        $usesHttps = ($parsed['scheme'] ?? '') === 'https';
        
        // Subdomain analysis
        $subdomains = explode('.', $domain);
        $hasMultipleSubdomains = count($subdomains) > 2;
        
        // Character analysis
        $containsHex = preg_match('/[0-9a-fA-F]{8,}/', $url);
        $containsNumbers = preg_match('/\d/', $domain);
        $containsSpecialChars = preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $url);
        
        // Path depth
        $urlDepth = $pathLength > 0 ? substr_count($parsed['path'], '/') - 1 : 0;
        
        // Random string detection
        $containsRandomString = preg_match('/[a-zA-Z0-9]{10,}/', $url);
        
        // TLD analysis
        $suspiciousTlds = ['.tk', '.ml', '.ga', '.cf', '.gq', '.xyz', '.top', '.club', '.site', '.online'];
        $suspiciousTld = false;
        foreach ($suspiciousTlds as $tld) {
            if (strpos($domain, $tld) !== false) {
                $suspiciousTld = true;
                break;
            }
        }
        
        // Brand name detection
        $legitimateBrands = ['google', 'facebook', 'youtube', 'amazon', 'microsoft', 'apple', 'netflix', 'twitter', 'instagram', 'linkedin'];
        $containsBrandName = false;
        $brandNameCount = 0;
        foreach ($legitimateBrands as $brand) {
            if (stripos($domain, $brand) !== false) {
                $containsBrandName = true;
                $brandNameCount++;
            }
        }
        
        // Suspicious words detection
        $suspiciousWords = ['login', 'signin', 'verify', 'secure', 'account', 'banking', 'paypal', 'ebay', 'amazon', 'update', 'confirm', 'verify', 'security'];
        $suspiciousWordCount = 0;
        foreach ($suspiciousWords as $word) {
            if (stripos($url, $word) !== false) {
                $suspiciousWordCount++;
            }
        }
        
        // Entropy calculation
        $entropyScore = $this->calculateEntropy($domain);
        
        // Additional features
        $hasRedirect = stripos($url, 'redirect') !== false || stripos($url, 'goto') !== false;
        $hasShortener = stripos($url, 'bit.ly') !== false || stripos($url, 'goo.gl') !== false || stripos($url, 'tinyurl') !== false;
        $hasTyposquatting = $this->detectTyposquatting($domain);
        $hasHomograph = $this->detectHomograph($domain);
        
        return [
            'URL_Length' => $urlLength,
            'Domain_Length' => $domainLength,
            'Path_Length' => $pathLength,
            'Query_Length' => $queryLength,
            'Dots_in_Domain' => $dotsInDomain,
            'Contains_IP' => $containsIp ? 1 : 0,
            'Contains_At' => $containsAt ? 1 : 0,
            'Uses_HTTPS' => $usesHttps ? 1 : 0,
            'Has_Multiple_Subdomains' => $hasMultipleSubdomains ? 1 : 0,
            'Contains_Hex' => $containsHex ? 1 : 0,
            'Contains_Numbers' => $containsNumbers ? 1 : 0,
            'Contains_Special_Chars' => $containsSpecialChars ? 1 : 0,
            'URL_Depth' => $urlDepth,
            'Contains_Random_String' => $containsRandomString ? 1 : 0,
            'Suspicious_TLD' => $suspiciousTld ? 1 : 0,
            'Contains_Brand_Name' => $containsBrandName ? 1 : 0,
            'Brand_Name_Count' => $brandNameCount,
            'Suspicious_Word_Count' => $suspiciousWordCount,
            'Entropy_Score' => $entropyScore,
            'Has_Redirect' => $hasRedirect ? 1 : 0,
            'Has_Shortener' => $hasShortener ? 1 : 0,
            'Has_Typosquatting' => $hasTyposquatting ? 1 : 0,
            'Has_Homograph' => $hasHomograph ? 1 : 0
        ];
    }
    
    /**
     * Calculate entropy for text
     */
    private function calculateEntropy($text) {
        if (empty($text)) return 0;
        
        $length = strlen($text);
        $freq = [];
        
        // Count character frequencies
        for ($i = 0; $i < $length; $i++) {
            $char = $text[$i];
            $freq[$char] = ($freq[$char] ?? 0) + 1;
        }
        
        // Calculate entropy
        $entropy = 0;
        foreach ($freq as $count) {
            $p = $count / $length;
            $entropy -= $p * log($p, 2);
        }
        
        return $entropy;
    }
    
    /**
     * Detect typosquatting
     */
    private function detectTyposquatting($domain) {
        $commonTypos = [
            'google' => ['g00gle', 'go0gle', 'g0ogle', 'goog1e', 'g00g1e'],
            'facebook' => ['faceb00k', 'faceb0ok', 'facebo0k', 'faceb00k'],
            'amazon' => ['amaz0n', 'amaz0n', 'amaz0n', 'amaz0n'],
            'paypal' => ['paypa1', 'paypa1', 'paypa1', 'paypa1'],
            'ebay' => ['ebay', 'ebay', 'ebay', 'ebay']
        ];
        
        foreach ($commonTypos as $brand => $typos) {
            if (stripos($domain, $brand) !== false) {
                foreach ($typos as $typo) {
                    if (stripos($domain, $typo) !== false) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    /**
     * Detect homograph attacks
     */
    private function detectHomograph($domain) {
        $homographChars = [
            'a' => ['а', 'а', 'а', 'а'],  // Cyrillic 'а'
            'e' => ['е', 'е', 'е', 'е'],  // Cyrillic 'е'
            'o' => ['о', 'о', 'о', 'о'],  // Cyrillic 'о'
            'c' => ['с', 'с', 'с', 'с'],  // Cyrillic 'с'
            'p' => ['р', 'р', 'р', 'р'],  // Cyrillic 'р'
            'x' => ['х', 'х', 'х', 'х'],  // Cyrillic 'х'
            'y' => ['у', 'у', 'у', 'у']   // Cyrillic 'у'
        ];
        
        foreach ($homographChars as $latin => $cyrillic) {
            foreach ($cyrillic as $char) {
                if (strpos($domain, $char) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Calculate risk score based on features
     */
    private function calculateRiskScore($features) {
        $score = 0;
        $weights = [
            'URL_Length' => 0.05,
            'Domain_Length' => 0.03,
            'Path_Length' => 0.02,
            'Query_Length' => 0.02,
            'Dots_in_Domain' => 0.08,
            'Contains_IP' => 0.15,
            'Contains_At' => 0.12,
            'Uses_HTTPS' => -0.05,
            'Has_Multiple_Subdomains' => 0.06,
            'Contains_Hex' => 0.08,
            'Contains_Numbers' => 0.04,
            'Contains_Special_Chars' => 0.06,
            'URL_Depth' => 0.03,
            'Contains_Random_String' => 0.10,
            'Suspicious_TLD' => 0.12,
            'Contains_Brand_Name' => -0.03,
            'Brand_Name_Count' => -0.02,
            'Suspicious_Word_Count' => 0.08,
            'Entropy_Score' => 0.05,
            'Has_Redirect' => 0.08,
            'Has_Shortener' => 0.06,
            'Has_Typosquatting' => 0.15,
            'Has_Homograph' => 0.15
        ];
        
        foreach ($features as $feature => $value) {
            if (isset($weights[$feature])) {
                if (is_numeric($value)) {
                    $score += $value * $weights[$feature];
                } else {
                    $score += $weights[$feature];
                }
            }
        }
        
        // Normalize score to 0-1 range
        return max(0, min(1, $score));
    }
    
    /**
     * Get risk level based on score
     */
    private function getRiskLevel($score) {
        if ($score >= 0.8) return 'VERY_HIGH';
        if ($score >= 0.6) return 'HIGH';
        if ($score >= 0.4) return 'MEDIUM';
        if ($score >= 0.2) return 'LOW';
        return 'VERY_LOW';
    }
    
    /**
     * Create expert analysis for fallback
     */
    private function createExpertAnalysis($url, $features, $isPhishing, $riskScore) {
        $parsed = parse_url($url);
        $domain = $parsed['host'] ?? '';
        
        $analysis = [
            'summary' => '',
            'risk_factors' => [],
            'security_features' => [],
            'recommendations' => [],
            'technical_details' => []
        ];
        
        // Determine summary
        if ($isPhishing) {
            $analysis['summary'] = "🚨 **PHISHING DETECTED**: This URL has been identified as a potential phishing threat with " . round($riskScore * 100, 1) . "% confidence.";
        } else {
            $analysis['summary'] = "✅ **SAFE**: This URL appears to be legitimate and safe to visit with " . round($riskScore * 100, 1) . "% confidence.";
        }
        
        // Analyze risk factors
        if ($features['Dots_in_Domain'] > 3) {
            $analysis['risk_factors'][] = "Multiple subdomains detected";
        }
        
        if ($features['Contains_IP']) {
            $analysis['risk_factors'][] = "Uses IP address instead of domain name";
        }
        
        if ($features['Contains_At']) {
            $analysis['risk_factors'][] = "Contains @ symbol (suspicious)";
        }
        
        if (!$features['Uses_HTTPS']) {
            $analysis['risk_factors'][] = "No HTTPS encryption";
        }
        
        if ($features['Contains_Random_String']) {
            $analysis['risk_factors'][] = "Contains random strings";
        }
        
        if ($features['Suspicious_TLD']) {
            $analysis['risk_factors'][] = "Uses suspicious top-level domain";
        }
        
        if ($features['Suspicious_Word_Count'] > 0) {
            $analysis['risk_factors'][] = "Contains " . $features['Suspicious_Word_Count'] . " suspicious keywords";
        }
        
        if ($features['Has_Typosquatting']) {
            $analysis['risk_factors'][] = "Potential typosquatting detected";
        }
        
        if ($features['Has_Homograph']) {
            $analysis['risk_factors'][] = "Potential homograph attack detected";
        }
        
        // Security features
        if ($features['Uses_HTTPS']) {
            $analysis['security_features'][] = "HTTPS encryption enabled";
        }
        
        if ($features['Contains_Brand_Name']) {
            $analysis['security_features'][] = "Contains legitimate brand name";
        }
        
        // Recommendations
        if ($isPhishing) {
            $analysis['recommendations'] = [
                "Do not visit this URL under any circumstances",
                "Do not enter any personal information",
                "Report this URL to your IT security team",
                "Consider adding this domain to your blacklist",
                "If you already visited this URL, change your passwords immediately"
            ];
        } else {
            $analysis['recommendations'] = [
                "This URL appears safe to visit",
                "Always verify you're on the correct domain",
                "Check for HTTPS encryption",
                "Be cautious with personal information"
            ];
        }
        
        // Technical details
        $analysis['technical_details'] = [
            'domain' => $domain,
            'url_length' => $features['URL_Length'],
            'domain_length' => $features['Domain_Length'],
            'entropy_score' => round($features['Entropy_Score'], 3),
            'suspicious_features_count' => array_sum([
                $features['Dots_in_Domain'] > 3,
                $features['Contains_IP'],
                $features['Contains_At'],
                !$features['Uses_HTTPS'],
                $features['Contains_Random_String'],
                $features['Suspicious_TLD'],
                $features['Suspicious_Word_Count'] > 0,
                $features['Has_Typosquatting'],
                $features['Has_Homograph']
            ])
        ];
        
        return $analysis;
    }
    
    /**
     * Make HTTP request to ML API
     */
    private function makeRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->apiUrl . $endpoint;
        
        $options = [
            'http' => [
                'method' => $method,
                'timeout' => $this->timeout,
                'header' => [
                    'Content-Type: application/json',
                    'User-Agent: PhishingDetector/1.0'
                ]
            ]
        ];
        
        if ($data && $method === 'POST') {
            $options['http']['content'] = json_encode($data);
        }
        
        $context = stream_context_create($options);
        
        for ($attempt = 1; $attempt <= $this->maxRetries; $attempt++) {
            try {
                $response = file_get_contents($url, false, $context);
                
                if ($response === false) {
                    throw new Exception("Failed to get response");
                }
                
                $decoded = json_decode($response, true);
                
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception("Invalid JSON response");
                }
                
                return $decoded;
                
            } catch (Exception $e) {
                if ($attempt === $this->maxRetries) {
                    throw new Exception("Request failed after {$this->maxRetries} attempts: " . $e->getMessage());
                }
                
                // Wait before retry
                usleep(100000 * $attempt); // 0.1s, 0.2s, 0.3s
            }
        }
        
        throw new Exception("Request failed");
    }
}
