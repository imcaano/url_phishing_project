<?php
namespace App\Controllers;

use App\Models\URLScan;
use App\Models\APIKey;

class APIController {
    private $urlScan;
    private $apiKey;
    
    public function __construct() {
        $this->urlScan = new URLScan();
        $this->apiKey = new APIKey();
    }
    
    public function scanURL() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['error' => 'Method not allowed'], 405);
        }
        
        $data = json_decode(file_get_contents('php://input'), true);
        $url = $data['url'] ?? '';
        
        if (empty($url)) {
            $this->jsonResponse(['error' => 'URL is required'], 400);
        }
        
        $result = $this->urlScan->scanURL($url);
        $this->jsonResponse($result);
    }
    
    public function getScanHistory() {
        $userId = $this->apiKey->getUserId($_SERVER['HTTP_X_API_KEY']);
        $history = $this->urlScan->getHistory($userId);
        $this->jsonResponse($history);
    }
    
    private function jsonResponse($data, $status = 200) {
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
} 