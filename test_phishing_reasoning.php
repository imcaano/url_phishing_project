<?php
// Test script to verify phishing detection reasoning with suspicious URL
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/URLScan.php';

use App\Models\URLScan;

// Create URLScan instance
$urlScan = new URLScan();

// Test clearly phishing URL
$phishingUrl = "http://192.168.1.1/paypal-secure-login.php?user=admin@fake-domain.com";

echo "Testing phishing detection reasoning with suspicious URL...\n";
echo "========================================================\n\n";

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
?> 