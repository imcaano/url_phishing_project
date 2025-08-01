<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\URLScan;
use App\Config\Database;
use PDO;

class ProfileController {
    private $user;
    private $urlScan;
    private $db;
    
    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        
        $this->user = new User();
        $this->urlScan = new URLScan();
        $this->db = Database::getDB();
    }
    
    public function index() {
        $userId = $_SESSION['user_id'];
        
        // Get user data
        $user = $this->user->getUserById($userId);
        if (!$user) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = "Error: User not found";
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        // Update session with latest user data
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        
        // Get user stats
        $stats = [
            'total_scans' => $this->urlScan->getUserTotalScans($userId),
            'phishing_detected' => $this->urlScan->getUserPhishingScans($userId),
            'safe_urls' => $this->urlScan->getUserSafeScans($userId)
        ];
        
        $success = isset($_SESSION['success']) ? $_SESSION['success'] : null;
        $message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
        
        // Clear session messages
        unset($_SESSION['success']);
        unset($_SESSION['message']);
        
        require_once __DIR__ . '/../views/profile.php';
    }
    
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /url_phishing_project/public/profile');
            exit;
        }
        
        $userId = $_SESSION['user_id'];
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        try {
            // Update basic info
            $updateData = ['username' => $username, 'email' => $email];
            
            // Handle password change if requested
            if (!empty($currentPassword) && !empty($newPassword)) {
                // Verify current password
                if (!$this->user->verifyPassword($userId, $currentPassword)) {
                    throw new \Exception("Current password is incorrect");
                }
                
                // Verify new passwords match
                if ($newPassword !== $confirmPassword) {
                    throw new \Exception("New passwords do not match");
                }
                
                // Enforce minimum password length
                if (strlen($newPassword) < 6) {
                    throw new \Exception("New password must be at least 6 characters long");
                }
                
                // Add new password to update data
                $updateData['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
            
            // Perform update
            if ($this->user->updateUser($userId, $updateData)) {
                $_SESSION['success'] = true;
                $_SESSION['message'] = "Profile updated successfully";
            } else {
                throw new \Exception("Failed to update profile");
            }
            
        } catch (\Exception $e) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = "Error updating profile: " . $e->getMessage();
        }
        
        header('Location: /url_phishing_project/public/profile');
        exit;
    }

    public function adminIndex() {
        // Get user data using the User model
        $user = $this->user->getUserById($_SESSION['user_id']);
        if (!$user) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = "Error: User not found";
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        // Get admin-specific stats
        $stats = $this->getAdminStats($_SESSION['user_id']);
        
        // Display the admin profile view
        require_once __DIR__ . '/../views/admin/profile.php';
    }

    public function adminUpdate() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /url_phishing_project/public/admin/profile');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate input
        if (empty($username) || empty($email)) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = 'Username and email are required';
            header('Location: /url_phishing_project/public/admin/profile');
            exit;
        }

        // Check if username or email is already taken by another user
        $existingUserByUsername = $this->user->getUserByUsernameOrEmail($username);
        $existingUserByEmail = $this->user->getUserByUsernameOrEmail($email);
        if (($existingUserByUsername && $existingUserByUsername['id'] != $userId) ||
            ($existingUserByEmail && $existingUserByEmail['id'] != $userId)) {
            $_SESSION['success'] = false;
            $_SESSION['message'] = 'Username or email is already taken';
            header('Location: /url_phishing_project/public/admin/profile');
            exit;
        }

        // If password change is requested
        if (!empty($newPassword)) {
            if ($newPassword !== $confirmPassword) {
                $_SESSION['success'] = false;
                $_SESSION['message'] = 'New passwords do not match';
                header('Location: /url_phishing_project/public/admin/profile');
                exit;
            }

            // Enforce minimum password length
            if (strlen($newPassword) < 6) {
                $_SESSION['success'] = false;
                $_SESSION['message'] = 'New password must be at least 6 characters long';
                header('Location: /url_phishing_project/public/admin/profile');
                exit;
            }

            // Verify current password
            $user = $this->user->getUserById($userId);
            if (!password_verify($currentPassword, $user['password'])) {
                $_SESSION['success'] = false;
                $_SESSION['message'] = 'Current password is incorrect';
                header('Location: /url_phishing_project/public/admin/profile');
                exit;
            }

            // Update with new password
            $updateData = [
                'username' => $username,
                'email' => $email,
                'password' => password_hash($newPassword, PASSWORD_DEFAULT)
            ];
        } else {
            // Update without changing password
            $updateData = [
                'username' => $username,
                'email' => $email
            ];
        }

        // Perform update using User model
        if ($this->user->updateUser($userId, $updateData)) {
            $_SESSION['success'] = true;
            $_SESSION['message'] = 'Profile updated successfully';
            $_SESSION['username'] = $username;
        } else {
            $_SESSION['success'] = false;
            $_SESSION['message'] = 'Failed to update profile';
        }

        header('Location: /url_phishing_project/public/admin/profile');
        exit;
    }

    private function getAdminStats($userId) {
        try {
            $db = Database::getDB();
            
            // Get total scans
            $scanQuery = "SELECT COUNT(*) as total_scans FROM url_scans";
            $scanStmt = $db->query($scanQuery);
            $totalScans = $scanStmt->fetch(PDO::FETCH_ASSOC)['total_scans'];

            // Get phishing detected
            $phishingQuery = "SELECT COUNT(*) as phishing_detected FROM url_scans WHERE status = 'phishing'";
            $phishingStmt = $db->query($phishingQuery);
            $phishingDetected = $phishingStmt->fetch(PDO::FETCH_ASSOC)['phishing_detected'];

            // Get users managed
            $usersQuery = "SELECT COUNT(*) as users_managed FROM users WHERE role = 'user'";
            $usersStmt = $db->query($usersQuery);
            $usersManaged = $usersStmt->fetch(PDO::FETCH_ASSOC)['users_managed'];

            // Get blacklisted domains
            $blacklistQuery = "SELECT COUNT(*) as domains_blacklisted FROM domain_blacklist";
            $blacklistStmt = $db->query($blacklistQuery);
            $domainsBlacklisted = $blacklistStmt->fetch(PDO::FETCH_ASSOC)['domains_blacklisted'];

            return [
                'total_scans' => $totalScans,
                'phishing_detected' => $phishingDetected,
                'users_managed' => $usersManaged,
                'domains_blacklisted' => $domainsBlacklisted
            ];
        } catch (\PDOException $e) {
            error_log("Error getting admin stats: " . $e->getMessage());
            return [
                'total_scans' => 0,
                'phishing_detected' => 0,
                'users_managed' => 0,
                'domains_blacklisted' => 0
            ];
        }
    }
} 