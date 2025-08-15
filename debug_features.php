<?php
// Debug script to see what features are being extracted
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/URLScan.php';

use App\Models\URLScan;

// Create URLScan instance
$urlScan = new URLScan();

// Test URL that should be phishing - using test mode to bypass domain reachability
$testUrl = "http://paypal-secure-login.xyz/login.php?secure=admin@fake-domain.com";

echo "Debugging feature extraction with TEST MODE...\n";
echo "=============================================\n\n";

try {
    // Perform scan to get features with test mode enabled
    $scanResult = $urlScan->scanURL($testUrl, null, false, true); // testMode = true
    
    echo "URL: $testUrl\n";
    echo "Domain: " . ($scanResult['domain'] ?? 'N/A') . "\n";
    echo "Is Phishing: " . ($scanResult['is_phishing'] ? 'Yes' : 'No') . "\n";
    echo "Status: " . ($scanResult['status'] ?? 'N/A') . "\n";
    if (isset($scanResult['error'])) {
        echo "Error: " . $scanResult['error'] . "\n";
    }
    echo "\n";
    
    if (isset($scanResult['features'])) {
        echo "All Extracted Features:\n";
        echo "=======================\n";
        foreach ($scanResult['features'] as $key => $value) {
            echo "$key: $value\n";
        }
        
        echo "\nChecking specific phishing indicators:\n";
        echo "=====================================\n";
        
        $checks = [
            'Contains IP Address' => 'Uses IP address instead of domain name',
            'Contains Special Chars' => 'Contains suspicious special characters',
            'Contains Random String' => 'Contains random strings',
            'Suspicious Words' => 'Contains suspicious keywords',
            'Uses HTTPS' => 'Not using secure HTTPS connection',
            'Brand Name Count' => 'Contains brand names',
            'URL Length' => 'Unusually long URL',
            'Dots in Domain' => 'Multiple subdomains',
            'Contains @ Symbol' => 'Contains @ symbol',
            'Contains Numbers in Domain' => 'Contains numbers in domain',
            'Contains Hexadecimal' => 'Contains hexadecimal characters',
            'Suspicious TLD' => 'Uses suspicious top-level domain',
            'Has Multiple Subdomains' => 'Has multiple subdomains',
            'Contains Brand Name' => 'Contains brand names',
            'Entropy Score' => 'High entropy score'
        ];
        
        foreach ($checks as $feature => $description) {
            if (isset($scanResult['features'][$feature])) {
                $value = $scanResult['features'][$feature];
                $status = "✓ Found: $value";
            } else {
                $status = "✗ Missing";
            }
            echo "$feature: $status\n";
        }
        
        echo "\nExpert Analysis:\n";
        echo "================\n";
        if (isset($scanResult['expert_analysis'])) {
            echo $scanResult['expert_analysis'] . "\n";
        }
    } else {
        echo "No features found in scan result.\n";
        echo "Scan result keys: " . implode(', ', array_keys($scanResult)) . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?> 