<?php
// Test script to verify improved phishing detection reasoning
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/URLScan.php';

use App\Models\URLScan;

// Create URLScan instance
$urlScan = new URLScan();

// Test phishing URL
$phishingUrl = "http://192.168.1.1/login.php?secure=paypal.com@fake-domain.com";

echo "Testing phishing detection reasoning...\n";
echo "=====================================\n\n";

try {
    // Perform scan
    $scanResult = $urlScan->scanURL($phishingUrl);
    
    if ($scanResult) {
        echo "Scan completed successfully!\n\n";
        
        // Display basic scan info
        echo "URL: " . ($scanResult['url'] ?? 'N/A') . "\n";
        echo "Domain: " . ($scanResult['domain'] ?? 'N/A') . "\n";
        echo "Is Phishing: " . ($scanResult['is_phishing'] ? 'Yes' : 'No') . "\n";
        echo "Status: " . ($scanResult['status'] ?? 'N/A') . "\n\n";
        
        // Display expert analysis
        if (isset($scanResult['expert_analysis'])) {
            echo "Expert Analysis:\n";
            echo "================\n";
            echo $scanResult['expert_analysis'] . "\n";
        }
        
        // Display features
        if (isset($scanResult['features'])) {
            echo "\nDetected Features:\n";
            echo "==================\n";
            foreach ($scanResult['features'] as $key => $value) {
                echo "$key: $value\n";
            }
        }
        
    } else {
        echo "Scan failed or returned null result.\n";
    }
    
} catch (Exception $e) {
    echo "Error during scan: " . $e->getMessage() . "\n";
}

echo "\n\nNow testing safe URL...\n";
echo "========================\n\n";

// Test safe URL
$safeUrl = "https://google.com";

try {
    // Perform scan
    $scanResult = $urlScan->scanURL($safeUrl);
    
    if ($scanResult) {
        echo "Scan completed successfully!\n\n";
        
        // Display basic scan info
        echo "URL: " . ($scanResult['url'] ?? 'N/A') . "\n";
        echo "Domain: " . ($scanResult['domain'] ?? 'N/A') . "\n";
        echo "Is Phishing: " . ($scanResult['is_phishing'] ? 'Yes' : 'No') . "\n";
        echo "Status: " . ($scanResult['status'] ?? 'N/A') . "\n\n";
        
        // Display expert analysis
        if (isset($scanResult['expert_analysis'])) {
            echo "Expert Analysis:\n";
            echo "================\n";
            echo $scanResult['expert_analysis'] . "\n";
        }
        
    } else {
        echo "Scan failed or returned null result.\n";
    }
    
} catch (Exception $e) {
    echo "Error during scan: " . $e->getMessage() . "\n";
}
?> 