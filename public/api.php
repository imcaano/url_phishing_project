<?php
require_once '../vendor/autoload.php';
require_once '../backend/config/Database.php';
require_once '../backend/models/URLScan.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    $urlScan = new App\Models\URLScan();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $url = $input['url'] ?? '';
        
        if (empty($url)) {
            http_response_code(400);
            echo json_encode(['error' => 'URL is required']);
            exit;
        }
        
        // Get user info from session or headers
        session_start();
        $userId = $_SESSION['user_id'] ?? null;
        $isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
        
        // Perform the scan
        $scanResult = $urlScan->scanURL($url, $userId, $isAdmin);
        
        if (isset($scanResult['error'])) {
            http_response_code(400);
            echo json_encode($scanResult);
            exit;
        }
        
        // Return the scan result
        echo json_encode([
            'success' => true,
            'scan_result' => $scanResult
        ]);
        
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
?> 