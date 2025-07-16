<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\URLScan;
use App\Models\APIKey;

class UserController {
    private $user;
    private $urlScan;
    private $apiKey;
    
    public function __construct() {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        
        $this->user = new User();
        $this->urlScan = new URLScan();
        $this->apiKey = new APIKey();
    }
    
    public function dashboard() {
        $userId = $_SESSION['user_id'];
        $recentScans = $this->urlScan->getRecentScans($userId, 5);
        include __DIR__ . '/../views/user/dashboard.php';
    }
    
    public function profile() {
        $userId = $_SESSION['user_id'];
        $user = $this->user->getUser($userId);
        $apiKeys = $this->apiKey->getUserKeys($userId);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'update_profile':
                    $this->updateProfile($userId);
                    break;
                    
                case 'generate_key':
                    $this->generateApiKey($userId);
                    break;
                    
                case 'toggle_key':
                    $this->toggleApiKey($userId, $_POST['key_id']);
                    break;
            }
        }
        
        include __DIR__ . '/../views/user/profile.php';
    }
    
    private function updateProfile($userId) {
        $currentPassword = $_POST['current_password'] ?? '';
        if (!$this->user->verifyPassword($userId, $currentPassword)) {
            $message = 'Current password is incorrect';
            $success = false;
            return;
        }
        
        $updates = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];
        
        if (!empty($_POST['new_password'])) {
            $updates['password'] = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        }
        
        $result = $this->user->updateUser($userId, $updates);
        
        $message = $result ? 'Profile updated successfully' : 'Failed to update profile';
        $success = $result;
    }
    
    private function generateApiKey($userId) {
        $result = $this->apiKey->generate($userId);
        if ($result) {
            header('Location: /profile');
            exit;
        }
    }
    
    private function toggleApiKey($userId, $keyId) {
        $this->apiKey->toggle($userId, $keyId);
        header('Location: /profile');
        exit;
    }
} 