<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\URLScan;
use App\Models\DomainBlacklist;
use App\Models\DomainInfo;

class AdminController {
    private $user;
    private $urlScan;
    private $blacklist;
    
    public function __construct() {
        $this->user = new User();
        $this->urlScan = new URLScan();
        $this->blacklist = new DomainBlacklist();
        $this->checkAdminAccess();
    }
    
    private function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
    }
    
    public function dashboard() {
        try {
            // Get statistics
            $totalUsers = $this->user->getTotalUsers();
            $totalScans = $this->urlScan->getTotalScans();
            $phishingScans = $this->urlScan->getPhishingCount();
            $blacklistedDomains = $this->blacklist->getTotalDomains();
            
            // Include the dashboard view with the data
            require_once __DIR__ . '/../views/admin/dashboard.php';
        } catch (\Exception $e) {
            error_log("Admin dashboard error: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading the dashboard.";
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
    }
    
    public function manageUsers() {
        $action = $_POST['action'] ?? '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetUserId = $_POST['user_id'] ?? null;
            $targetUser = $targetUserId ? $this->user->getUserById($targetUserId) : null;
            switch ($action) {
                case 'update':
                    if ($targetUser && $targetUser['role'] === 'admin' && $targetUser['id'] != $_SESSION['user_id']) {
                        $_SESSION['error'] = 'You cannot update another admin.';
                        break;
                    }
                    $this->user->updateUser($_POST['user_id'], [
                        'status' => $_POST['status'],
                        'role' => $_POST['role']
                    ]);
                    break;
                    
                case 'delete':
                    if ($targetUser && $targetUser['role'] === 'admin') {
                        $_SESSION['error'] = 'You cannot delete another admin.';
                        break;
                    }
                    $this->user->deleteUser($_POST['user_id']);
                    break;
            }
        }
        
        $users = $this->user->getAllUsers();
        include __DIR__ . '/../views/admin/users.php';
    }
    
    public function reports() {
        try {
            // Migrate existing scans if needed
            if (isset($_GET['migrate']) && $_GET['migrate'] === 'true') {
                $migratedCount = $this->urlScan->migrateExistingScans();
                $_SESSION['success'] = "Successfully migrated $migratedCount domains to the new system.";
                header('Location: /url_phishing_project/public/admin/reports');
                exit;
            }
            
            $filters = [];
            if (isset($_GET['domain']) && $_GET['domain']) {
                $filters['domain'] = $_GET['domain'];
            }
            if (isset($_GET['date_from']) && $_GET['date_from']) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (isset($_GET['status']) && $_GET['status']) {
                $filters['status'] = $_GET['status'];
            }
            if (isset($_GET['risk_level']) && $_GET['risk_level']) {
                $filters['risk_level'] = $_GET['risk_level'];
            }
            
            // Get scanned domains and stats
            $scannedDomains = $this->urlScan->getScannedDomains($filters);
            $domainStats = $this->urlScan->getScannedDomainStats();
            
            // Get individual scans for detailed view
            $individualScans = $this->urlScan->getAllScans();
            
            include __DIR__ . '/../views/admin/reports.php';
        } catch (\Exception $e) {
            error_log("Error in reports: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading reports.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }

    public function reportDetails($id) {
        try {
            // Get domain information from scanned_domains table
            $domain = $this->urlScan->getScannedDomainById($id);
            if (!$domain) {
                $_SESSION['error'] = "Domain report not found.";
                header('Location: /url_phishing_project/public/admin/reports');
                exit;
            }
            
            // Get recent scans for this domain
            $recentScans = $this->urlScan->getRecentScansByDomain($domain['domain'], 10);
            
            include __DIR__ . '/../views/admin/report_details.php';
        } catch (\Exception $e) {
            error_log("Error in report details: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading report details.";
            header('Location: /url_phishing_project/public/admin/reports');
            exit;
        }
    }

    public function blacklist() {
        try {
            $blacklist = new DomainBlacklist();
            
            // Handle POST actions
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['action'])) {
                    switch ($_POST['action']) {
                        case 'add':
                            if (isset($_POST['domain'])) {
                                $domain = trim($_POST['domain']);
                                $reason = isset($_POST['reason']) ? trim($_POST['reason']) : 'Admin blacklisted';
                                
                                // Check if this is an AJAX request
                                if ($this->isAjaxRequest()) {
                                    try {
                                        if ($blacklist->addDomain($domain, $reason, $_SESSION['user_id'])) {
                                            echo json_encode([
                                                'success' => true,
                                                'message' => 'Domain "' . htmlspecialchars($domain) . '" added to blacklist successfully'
                                            ]);
                                        } else {
                                            echo json_encode([
                                                'success' => false,
                                                'message' => 'Failed to add domain to blacklist'
                                            ]);
                                        }
                                    } catch (\Exception $e) {
                                        echo json_encode([
                                            'success' => false,
                                            'message' => 'Error: ' . $e->getMessage()
                                        ]);
                                    }
                                    return;
                                } else {
                                    // Regular form submission
                                    if ($blacklist->addDomain($domain, $reason, $_SESSION['user_id'])) {
                                        $_SESSION['success'] = "Domain added to blacklist successfully.";
                                    } else {
                                        $_SESSION['error'] = "Failed to add domain to blacklist.";
                                    }
                                }
                            }
                            break;
                            
                        case 'delete':
                            if (isset($_POST['domain_id']) && !empty($_POST['domain_id'])) {
                                $domainId = (int)$_POST['domain_id'];
                                error_log("Attempting to delete domain with ID: " . $domainId);
                                
                                // First, get the domain info to confirm it exists
                                $domainInfo = $blacklist->getDomainById($domainId);
                                if ($domainInfo) {
                                    error_log("Found domain: " . json_encode($domainInfo));
                                    if ($blacklist->deleteDomain($domainId)) {
                                        $_SESSION['success'] = "Domain '{$domainInfo['domain']}' removed from blacklist successfully.";
                                        error_log("Successfully deleted domain ID: " . $domainId);
                                    } else {
                                        $_SESSION['error'] = "Failed to remove domain from blacklist.";
                                        error_log("Failed to delete domain ID: " . $domainId);
                                    }
                                } else {
                                    $_SESSION['error'] = "Domain not found in blacklist.";
                                    error_log("Domain ID not found: " . $domainId);
                                }
                            } else {
                                $_SESSION['error'] = "Invalid domain ID provided.";
                                error_log("No domain_id provided in delete request. POST data: " . json_encode($_POST));
                            }
                            break;
                    }
                }
                
                // Only redirect if not AJAX request
                if (!$this->isAjaxRequest()) {
                    header('Location: /url_phishing_project/public/admin/blacklist');
                    exit;
                }
            }
            
            // Get all blacklisted domains with report counts
            $domains = $blacklist->getAllDomainsWithReports();
            
            // Set message if exists
            $message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
            $success = isset($_SESSION['success']);
            
            // Clear messages
            unset($_SESSION['success'], $_SESSION['error']);
            
            include __DIR__ . '/../views/admin/blacklist.php';
        } catch (\Exception $e) {
            error_log("Error in blacklist: " . $e->getMessage());
            if ($this->isAjaxRequest()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'An error occurred while managing the blacklist.'
                ]);
                return;
            } else {
                $_SESSION['error'] = "An error occurred while managing the blacklist.";
                header('Location: /url_phishing_project/public/admin/dashboard');
                exit;
            }
        }
    }
    
    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    public function users() {
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $targetUserId = $_POST['user_id'] ?? null;
            $targetUser = $targetUserId ? $this->user->getUserById($targetUserId) : null;
            switch ($action) {
                case 'add':
                    $result = $this->user->create([
                        'username' => $_POST['username'],
                        'email' => $_POST['email'],
                        'password' => $_POST['password'],
                        'role' => $_POST['role'] ?? 'user'
                    ]);
                    if ($result['success']) {
                        $_SESSION['success'] = true;
                        $_SESSION['message'] = 'User added successfully.';
                    } else {
                        $_SESSION['success'] = false;
                        $_SESSION['message'] = $result['error'] ?? 'Failed to add user.';
                    }
                    break;
                case 'update':
                    if ($targetUser && $targetUser['role'] === 'admin' && $targetUser['id'] != $_SESSION['user_id']) {
                        $_SESSION['success'] = false;
                        $_SESSION['message'] = 'You cannot update another admin.';
                        break;
                    }
                    $this->user->updateUser($_POST['user_id'], [
                        'status' => $_POST['status'],
                        'role' => $_POST['role']
                    ]);
                    break;
                case 'delete':
                    if ($targetUser && $targetUser['role'] === 'admin') {
                        $_SESSION['success'] = false;
                        $_SESSION['message'] = 'You cannot delete another admin.';
                        break;
                    }
                    if ($this->user->deleteUser($_POST['user_id'])) {
                        $_SESSION['success'] = true;
                        $_SESSION['message'] = 'User deleted successfully.';
                    } else {
                        $_SESSION['success'] = false;
                        $_SESSION['message'] = 'Failed to delete user.';
                    }
                    break;
            }
            header('Location: /url_phishing_project/public/admin/users');
            exit;
        }
        $users = $this->user->getAllUsers();
        $message = $_SESSION['message'] ?? null;
        $success = $_SESSION['success'] ?? null;
        unset($_SESSION['message'], $_SESSION['success']);
        include __DIR__ . '/../views/admin/users.php';
    }

    public function scan() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $url = $_POST['url'] ?? '';
                $userId = $_SESSION['user_id'] ?? null;
                $isAdmin = true; // This is admin scan
                
                if (!empty($url)) {
                    // Use R1 model for scanning - it already includes WHOIS and phishing detection
                    $scanResult = $this->urlScan->scanURL($url, $userId, $isAdmin);
                    
                    if ($scanResult === false) {
                        $error = "Failed to scan URL. Please try again.";
                    } else {
                        // Store scan result in session for display
                        $_SESSION['scan_result'] = $scanResult;
                    }
                    // scanResult already contains all necessary data from R1 model
                } else {
                    $error = "Please enter a valid URL.";
                }
            }
            
            // Display scan result if available
            $scanResult = $_SESSION['scan_result'] ?? null;
            if ($scanResult) {
                unset($_SESSION['scan_result']); // Clear after display
            }
            
            require_once __DIR__ . '/../views/admin/scan.php';
        } catch (\Exception $e) {
            error_log("Error in admin scan: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while scanning the URL.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }
    
    public function profile() {
        try {
            $userId = $_SESSION['user_id'];
            $user = $this->user->getUser($userId);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $action = $_POST['action'] ?? '';
                
                if ($action === 'update_profile') {
                    $this->updateAdminProfile($userId);
                }
            }
            
            // Get messages
            $message = $_SESSION['success'] ?? $_SESSION['error'] ?? null;
            $success = isset($_SESSION['success']);
            
            // Clear messages
            unset($_SESSION['success'], $_SESSION['error']);
            
            include __DIR__ . '/../views/admin/profile.php';
        } catch (\Exception $e) {
            error_log("Error in admin profile: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while updating profile.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }
    
    private function updateAdminProfile($userId) {
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
            
            if (strlen($newPassword) < 8) {
                $_SESSION['error'] = 'New password must be at least 8 characters long';
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
} 