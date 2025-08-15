<?php
// Test script to verify WHOIS information display
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/backend/models/URLScan.php';

use App\Models\URLScan;

// Create URLScan instance
$urlScan = new URLScan();

// Test domain
$testUrl = "https://edu.so";

echo "Testing WHOIS information for: $testUrl\n";
echo "=====================================\n\n";

try {
    // Perform scan
    $scanResult = $urlScan->scanURL($testUrl);
    
    if ($scanResult) {
        echo "Scan completed successfully!\n\n";
        
        // Display basic scan info
        echo "URL: " . ($scanResult['url'] ?? 'N/A') . "\n";
        echo "Domain: " . ($scanResult['domain'] ?? 'N/A') . "\n";
        echo "Is Phishing: " . ($scanResult['is_phishing'] ? 'Yes' : 'No') . "\n";
        echo "Status: " . ($scanResult['status'] ?? 'N/A') . "\n\n";
        
        // Display WHOIS information
        if (isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])) {
            echo "WHOIS Information:\n";
            echo "==================\n";
            foreach ($scanResult['whois_info'] as $key => $value) {
                echo "$key: $value\n";
            }
        } else {
            echo "No WHOIS information available.\n";
        }
        
        // Display expert analysis
        if (isset($scanResult['expert_analysis'])) {
            echo "\nExpert Analysis:\n";
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