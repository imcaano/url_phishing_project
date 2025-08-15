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
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        // If admin, redirect to admin dashboard
        if ($_SESSION['role'] !== 'user') {
            header('Location: /url_phishing_project/public/admin/dashboard');
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
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate current password
        if (!$this->user->verifyPassword($userId, $currentPassword)) {
            $_SESSION['error'] = 'Current password is incorrect';
            return;
        }
        
        // If changing password, validate new password
        if (!empty($newPassword)) {
            if ($newPassword !== $confirmPassword) {
                $_SESSION['error'] = 'New password and confirm password do not match';
                return;
            }
            
            if (strlen($newPassword) < 6) {
                $_SESSION['error'] = 'New password must be at least 6 characters long';
                return;
            }
        }
        
        $updates = [
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? ''
        ];
        
        if (!empty($newPassword)) {
            $updates['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        
        $result = $this->user->updateUser($userId, $updates);
        
        if ($result) {
            $_SESSION['success'] = 'Profile updated successfully';
        } else {
            $_SESSION['error'] = 'Failed to update profile';
        }
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