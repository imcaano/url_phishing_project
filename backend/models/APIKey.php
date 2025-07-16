<?php
namespace App\Models;

use App\Config\Database;

class APIKey {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    public function generate($userId) {
        $apiKey = bin2hex(random_bytes(32));
        
        $stmt = $this->db->prepare(
            "INSERT INTO api_keys (user_id, api_key) VALUES (?, ?)"
        );
        $stmt->bind_param('is', $userId, $apiKey);
        
        if ($stmt->execute()) {
            return $apiKey;
        }
        return false;
    }
    
    public function validate($apiKey) {
        $stmt = $this->db->prepare(
            "SELECT user_id, daily_limit, requests_today, last_reset_date 
            FROM api_keys 
            WHERE api_key = ? AND is_active = 1"
        );
        $stmt->bind_param('s', $apiKey);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($key = $result->fetch_assoc()) {
            // Reset daily counter if it's a new day
            if ($key['last_reset_date'] != date('Y-m-d')) {
                $this->resetDailyCounter($apiKey);
                return true;
            }
            
            // Check if daily limit is reached
            if ($key['requests_today'] >= $key['daily_limit']) {
                return false;
            }
            
            $this->incrementRequests($apiKey);
            return true;
        }
        
        return false;
    }
    
    private function resetDailyCounter($apiKey) {
        $stmt = $this->db->prepare(
            "UPDATE api_keys 
            SET requests_today = 1, 
                last_reset_date = CURRENT_DATE,
                last_used = CURRENT_TIMESTAMP 
            WHERE api_key = ?"
        );
        $stmt->bind_param('s', $apiKey);
        $stmt->execute();
    }
    
    private function incrementRequests($apiKey) {
        $stmt = $this->db->prepare(
            "UPDATE api_keys 
            SET requests_today = requests_today + 1,
                last_used = CURRENT_TIMESTAMP 
            WHERE api_key = ?"
        );
        $stmt->bind_param('s', $apiKey);
        $stmt->execute();
    }
    
    public function getUserKeys($userId) {
        $stmt = $this->db->prepare(
            "SELECT * FROM api_keys WHERE user_id = ?"
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
} 