<?php
// Direct test of WhoisXML API
$apiKey = 'at_XyoHW2UIWj9z7iPrszj1MbfKddcm4';
$domain = 'edu.so';
$endpoint = "https://www.whoisxmlapi.com/whoisserver/WhoisService?apiKey={$apiKey}&domainName={$domain}&outputFormat=json";

echo "Testing WhoisXML API directly...\n";
echo "Endpoint: $endpoint\n";
echo "=====================================\n\n";

$ch = curl_init($endpoint);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');

$response = curl_exec($ch);
$curlError = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($curlError) {
    echo "CURL Error: $curlError\n";
    exit;
}

if ($httpCode !== 200) {
    echo "HTTP Error: $httpCode\n";
    echo "Response: $response\n";
    exit;
}

echo "HTTP Code: $httpCode\n";
echo "Response received successfully!\n\n";

// Parse JSON response
$jsonData = json_decode($response, true);

if (!$jsonData) {
    echo "Failed to parse JSON response\n";
    echo "Raw response: $response\n";
    exit;
}

echo "Parsed JSON successfully!\n\n";

if (isset($jsonData['WhoisRecord'])) {
    $whoisRecord = $jsonData['WhoisRecord'];
    
    echo "WHOIS Information for domain: " . ($whoisRecord['domainName'] ?? 'Unknown') . "\n";
    echo "=====================================\n";
    
    // Display key information
    echo "Domain Name: " . ($whoisRecord['domainName'] ?? 'Unknown') . "\n";
    echo "Domain Availability: " . ($whoisRecord['domainAvailability'] ?? 'Unknown') . "\n";
    echo "Registrar Name: " . ($whoisRecord['registrarName'] ?? 'Unknown') . "\n";
    echo "Contact Email: " . ($whoisRecord['contactEmail'] ?? 'Unknown') . "\n";
    echo "Estimated Domain Age: " . ($whoisRecord['estimatedDomainAge'] ?? 'Unknown') . " days\n";
    
    if (isset($whoisRecord['registryData'])) {
        $registryData = $whoisRecord['registryData'];
        echo "\nRegistry Data:\n";
        echo "Created Date: " . ($registryData['createdDate'] ?? 'Unknown') . "\n";
        echo "Updated Date: " . ($registryData['updatedDate'] ?? 'Unknown') . "\n";
        echo "Expires Date: " . ($registryData['expiresDate'] ?? 'Unknown') . "\n";
        
        if (isset($registryData['registrant'])) {
            $registrant = $registryData['registrant'];
            echo "Registrant Name: " . ($registrant['name'] ?? 'Unknown') . "\n";
        }
    }
    
    echo "\nFull JSON Response:\n";
    echo json_encode($jsonData, JSON_PRETTY_PRINT);
    
} else {
    echo "No WhoisRecord found in response\n";
    echo "Full response: " . json_encode($jsonData, JSON_PRETTY_PRINT);
}
?> 