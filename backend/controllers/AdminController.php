<?php
namespace App\Controllers;

use App\Models\User;
use App\Models\URLScan;
use App\Models\DomainBlacklist;
use App\Models\DomainInfo;
use PDO;

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
            
            // Get domain statistics
            $domainStats = $this->urlScan->getScannedDomainStats();
            $totalDomains = $domainStats['total_domains'] ?? 0;
            $phishingDomains = $domainStats['phishing_domains'] ?? 0;
            $safeDomains = $domainStats['safe_domains'] ?? 0;
            
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
            
            // Sync blacklist status if needed
            if (isset($_GET['sync_blacklist']) && $_GET['sync_blacklist'] === 'true') {
                $syncedCount = $this->urlScan->syncBlacklistStatus();
                $_SESSION['success'] = "Successfully synced blacklist status for $syncedCount domains.";
                header('Location: /url_phishing_project/public/admin/reports');
                exit;
            }
            
            // Handle delete action
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
                $domain = $_POST['domain'] ?? '';
                if ($domain) {
                    try {
                        $pdo = \App\Config\Database::getDB();
                        
                        // Debug: Check table structure
                        error_log("Attempting to delete domain: $domain");
                        
                        // Check if tables exist and get their structure
                        $tables = ['scanned_domains', 'url_scans', 'domain_blacklist'];
                        foreach ($tables as $table) {
                            try {
                                $stmt = $pdo->query("DESCRIBE $table");
                                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                                error_log("Table $table columns: " . implode(', ', $columns));
                            } catch (\Exception $e) {
                                error_log("Error checking table $table: " . $e->getMessage());
                            }
                        }
                        
                        // First, check if the domain exists in the tables
                        // Try different possible column names
                        $possibleColumns = ['domain', 'url', 'host', 'domain_name'];
                        
                        // Check scanned_domains table
                        $scannedCount = 0;
                        foreach ($possibleColumns as $col) {
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM scanned_domains WHERE $col = ?");
                                $stmt->execute([$domain]);
                                $scannedCount = $stmt->fetchColumn();
                                if ($scannedCount > 0) {
                                    error_log("Found domain in scanned_domains using column: $col");
                                    break;
                                }
                            } catch (\Exception $e) {
                                // Column doesn't exist, try next one
                                continue;
                            }
                        }
                        
                        // Check url_scans table
                        $urlScansCount = 0;
                        $urlScansColumn = null;
                        foreach ($possibleColumns as $col) {
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM url_scans WHERE $col = ?");
                    $stmt->execute([$domain]);
                                $urlScansCount = $stmt->fetchColumn();
                                if ($urlScansCount > 0) {
                                    $urlScansColumn = $col;
                                    error_log("Found domain in url_scans using column: $col");
                                    break;
                                }
                            } catch (\Exception $e) {
                                // Column doesn't exist, try next one
                                continue;
                            }
                        }
                        
                        error_log("Deleting domain: $domain - Found in scanned_domains: $scannedCount, url_scans: $urlScansCount");
                        
                        // Delete from scanned_domains table if exists
                        if ($scannedCount > 0) {
                            // Find the correct column name for scanned_domains
                            $scannedColumn = null;
                            foreach ($possibleColumns as $col) {
                                try {
                                    $stmt = $pdo->prepare("DELETE FROM scanned_domains WHERE $col = ?");
                                    $deleted = $stmt->execute([$domain]);
                                    if ($deleted) {
                                        $scannedColumn = $col;
                                        error_log("Deleted from scanned_domains using column: $col");
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                        
                        // Delete from url_scans table if exists
                        if ($urlScansCount > 0 && $urlScansColumn) {
                            $stmt = $pdo->prepare("DELETE FROM url_scans WHERE $urlScansColumn = ?");
                            $deleted = $stmt->execute([$domain]);
                            error_log("Deleted from url_scans: " . ($deleted ? 'success' : 'failed'));
                        }
                        
                        // Also check if it's in domain_blacklist and remove if present
                        $blacklistCount = 0;
                        foreach ($possibleColumns as $col) {
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM domain_blacklist WHERE $col = ?");
                    $stmt->execute([$domain]);
                                $blacklistCount = $stmt->fetchColumn();
                                if ($blacklistCount > 0) {
                                    error_log("Found domain in domain_blacklist using column: $col");
                                    break;
                                }
                            } catch (\Exception $e) {
                                continue;
                            }
                        }
                        
                        if ($blacklistCount > 0) {
                            // Find the correct column name for domain_blacklist
                            foreach ($possibleColumns as $col) {
                                try {
                                    $stmt = $pdo->prepare("DELETE FROM domain_blacklist WHERE $col = ?");
                                    $deleted = $stmt->execute([$domain]);
                                    if ($deleted) {
                                        error_log("Deleted from domain_blacklist using column: $col");
                                        break;
                                    }
                                } catch (\Exception $e) {
                                    continue;
                                }
                            }
                        }
                        
                        $_SESSION['success'] = "Domain '$domain' has been deleted successfully from all tables.";
                        header('Location: /url_phishing_project/public/admin/reports');
                        exit;
                    } catch (\Exception $e) {
                        error_log("Error deleting domain '$domain': " . $e->getMessage());
                        error_log("Stack trace: " . $e->getTraceAsString());
                        $_SESSION['error'] = "Failed to delete domain. Error: " . $e->getMessage();
                    header('Location: /url_phishing_project/public/admin/reports');
                    exit;
                    }
                }
            }
            
            $filters = [];
            if (isset($_GET['domain']) && $_GET['domain']) {
                $filters['domain'] = $_GET['domain'];
            }
            if (isset($_GET['date_from']) && $_GET['date_from']) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (isset($_GET['date_to']) && $_GET['date_to']) {
                $filters['date_to'] = $_GET['date_to'];
            }
            if (isset($_GET['status']) && $_GET['status']) {
                $filters['status'] = $_GET['status'];
                // Handle blacklisted filter specifically
                if ($_GET['status'] === 'blacklisted') {
                    $filters['is_blacklisted'] = 1;
                }
            }

            
            // Get scanned domains and stats
            $scannedDomains = $this->urlScan->getScannedDomains($filters);
            $domainStats = $this->urlScan->getScannedDomainStats();
            
            // Get additional statistics
            $totalUsers = $this->user->getTotalUsers();
            $blacklistedDomains = $this->blacklist->getTotalDomains();
            
            // Add to domainStats
            $domainStats['total_users'] = $totalUsers;
            $domainStats['blacklisted_domains'] = $blacklistedDomains;
            
            // Get individual scans for detailed view
            $individualScans = $this->urlScan->getAllScans();
            
            // Get import history for display
            $importHistory = $this->getImportHistory($filters);
            
            include __DIR__ . '/../views/admin/reports.php';
        } catch (\Exception $e) {
            error_log("Error in reports: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while loading reports.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }

    public function reportDetails() {
        try {
            $domain = $_GET['domain'] ?? '';
            if (empty($domain)) {
                $_SESSION['error'] = "No domain specified.";
                header('Location: /url_phishing_project/public/admin/reports');
                exit;
            }
            
            // Get domain report information
            $domainReport = $this->urlScan->getScannedDomains(['domain' => $domain]);
            $report = !empty($domainReport) ? $domainReport[0] : null;
            
            if (!$report) {
                $_SESSION['error'] = "Report not found for domain: " . htmlspecialchars($domain);
                header('Location: /url_phishing_project/public/admin/reports');
                exit;
            }
            
            // Get detailed scan information for this domain
            $domainInfo = new DomainInfo();
            $whoisData = $domainInfo->getDomainInfo($domain);
            
            // Get recent scans for this domain
            $recentScans = $this->urlScan->getFilteredScans(['domain' => $domain]);
            
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
                            
                        case 'restore':
                            $domainId = null;
                            $domainName = null;
                            
                            // Check if domain_id is provided
                            if (isset($_POST['domain_id']) && !empty($_POST['domain_id'])) {
                                $domainId = (int)$_POST['domain_id'];
                                error_log("Attempting to restore domain with ID: " . $domainId);
                            }
                            // Check if domain name is provided (for reports page)
                            elseif (isset($_POST['domain']) && !empty($_POST['domain'])) {
                                $domainName = $_POST['domain'];
                                error_log("Attempting to restore domain by name: " . $domainName);
                                
                                // Get domain ID from domain name
                                $domainInfo = $blacklist->getDomainByDomain($domainName);
                                if ($domainInfo) {
                                    $domainId = $domainInfo['id'];
                                    error_log("Found domain ID " . $domainId . " for domain name " . $domainName);
                                } else {
                                    error_log("Domain name not found in blacklist: " . $domainName);
                                    if ($this->isAjaxRequest()) {
                                        echo json_encode([
                                            'success' => false,
                                            'message' => 'Domain not found in blacklist.'
                                        ]);
                                        return;
                                    } else {
                                        $_SESSION['error'] = "Domain not found in blacklist.";
                                    }
                                    break;
                                }
                            }
                            
                            if ($domainId) {
                                // First, get the domain info to confirm it exists
                                $domainInfo = $blacklist->getDomainById($domainId);
                                if ($domainInfo) {
                                    error_log("Found domain: " . json_encode($domainInfo));
                                    if ($blacklist->restoreDomain($domainId)) {
                                        if ($this->isAjaxRequest()) {
                                            echo json_encode([
                                                'success' => true,
                                                'message' => "Domain '{$domainInfo['domain']}' restored as safe and removed from blacklist successfully."
                                            ]);
                                            return;
                                        } else {
                                            $_SESSION['success'] = "Domain '{$domainInfo['domain']}' restored as safe and removed from blacklist successfully.";
                                        }
                                        error_log("Successfully restored domain ID: " . $domainId);
                                    } else {
                                        if ($this->isAjaxRequest()) {
                                            echo json_encode([
                                                'success' => false,
                                                'message' => 'Failed to restore domain from blacklist.'
                                            ]);
                                            return;
                                        } else {
                                            $_SESSION['error'] = "Failed to restore domain from blacklist.";
                                        }
                                        error_log("Failed to restore domain ID: " . $domainId);
                                    }
                                } else {
                                    if ($this->isAjaxRequest()) {
                                        echo json_encode([
                                            'success' => false,
                                            'message' => 'Domain not found in blacklist.'
                                        ]);
                                        return;
                                    } else {
                                        $_SESSION['error'] = "Domain not found in blacklist.";
                                    }
                                    error_log("Domain ID not found: " . $domainId);
                                }
                            } else {
                                if ($this->isAjaxRequest()) {
                                    echo json_encode([
                                        'success' => false,
                                        'message' => 'No domain ID or domain name provided.'
                                    ]);
                                    return;
                                } else {
                                    $_SESSION['error'] = "No domain ID or domain name provided.";
                                }
                                error_log("No domain_id or domain provided in restore request. POST data: " . json_encode($_POST));
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
                                        if ($this->isAjaxRequest()) {
                                            echo json_encode([
                                                'success' => true,
                                                'message' => "Domain '{$domainInfo['domain']}' removed from blacklist successfully."
                                            ]);
                                            return;
                                        } else {
                                        $_SESSION['success'] = "Domain '{$domainInfo['domain']}' removed from blacklist successfully.";
                                        }
                                        error_log("Successfully deleted domain ID: " . $domainId);
                                    } else {
                                        if ($this->isAjaxRequest()) {
                                            echo json_encode([
                                                'success' => false,
                                                'message' => 'Failed to remove domain from blacklist.'
                                            ]);
                                            return;
                                    } else {
                                        $_SESSION['error'] = "Failed to remove domain from blacklist.";
                                        }
                                        error_log("Failed to delete domain ID: " . $domainId);
                                    }
                                } else {
                                    if ($this->isAjaxRequest()) {
                                        echo json_encode([
                                            'success' => false,
                                            'message' => 'Domain not found in blacklist.'
                                        ]);
                                        return;
                                } else {
                                    $_SESSION['error'] = "Domain not found in blacklist.";
                                    }
                                    error_log("Domain ID not found: " . $domainId);
                                }
                            } else {
                                if ($this->isAjaxRequest()) {
                                    echo json_encode([
                                        'success' => false,
                                        'message' => 'Invalid domain ID provided.'
                                    ]);
                                    return;
                            } else {
                                $_SESSION['error'] = "Invalid domain ID provided.";
                                }
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
                
                if (!empty($url)) {
                    // Use ML model for scanning (same as user scan)
                    $scanResult = $this->scanWithML($url);
                    
                    if ($scanResult['status'] === 'success') {
                        // Get WHOIS data from R1 system
                        $whoisData = $this->getWhoisFromR1($scanResult['url']);
                        if (!empty($whoisData)) {
                            $scanResult['whois_info'] = $whoisData;
                    } else {
                            $scanResult['whois_info'] = [];
                        }
                        
                        // Add user ID and scan date for database storage
                        $scanResult['user_id'] = $userId;
                        $scanResult['scan_date'] = date('Y-m-d H:i:s');
                        
                        // Save scan result to database for reports
                        $this->urlScan->saveScanResult($scanResult, $userId);
                        
                        // Auto-add phishing domains to blacklist
                        if (isset($scanResult['prediction']) && $scanResult['prediction'] === 'phishing') {
                            $this->autoAddToBlacklist($scanResult['url']);
                        }
                        
                        // Store scan result in session for display
                        $_SESSION['scan_result'] = $scanResult;
                    } else {
                        $error = $scanResult['error'] ?? "Failed to scan URL. Please try again.";
                    }
                } else {
                    $error = "Please enter a valid URL.";
                }
            }
            
            // Display scan result if available
            $scanResult = $_SESSION['scan_result'] ?? null;
            if ($scanResult) {
                unset($_SESSION['scan_result']); // Clear after display
            }
            
            // Pass error to view
            $viewError = $error ?? null;
            
            // Pass scan result to view
            $viewScanResult = $scanResult;
            
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
        
        // Debug logging
        error_log("Password update attempt for user ID: " . $userId);
        error_log("Current password provided: " . (!empty($currentPassword) ? 'Yes' : 'No'));
        error_log("New password provided: " . (!empty($newPassword) ? 'Yes' : 'No'));
        
        // Validate current password
        if (!$this->user->verifyPassword($userId, $currentPassword)) {
            error_log("Current password verification failed for user ID: " . $userId);
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
            $updates['password'] = $newPassword; // Let the User model hash it
            error_log("Password will be updated for user ID: " . $userId);
        }
        
        $result = $this->user->updateUser($userId, $updates);
        
        if ($result) {
            error_log("Profile update successful for user ID: " . $userId);
            $_SESSION['success'] = 'Profile updated successfully';
        } else {
            error_log("Profile update failed for user ID: " . $userId);
            $_SESSION['error'] = 'Failed to update profile';
        }
    }
    
    public function scanWithML($url) {
        try {
            // First, validate URL format
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                return [
                    'error' => 'Invalid URL format. Please enter a valid URL.',
                    'status' => 'error'
                ];
            }
            
            // Extract domain from URL
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) {
                return [
                    'error' => 'Invalid domain format',
                    'status' => 'error'
                ];
            }
            
            // Use R1 system to check if domain is reachable and valid
            // But don't reject phishing domains - they should be scanned
            $r1Validation = $this->urlScan->scanURL($url, null, false, true); // testMode = true for validation only
            
            // Only reject if domain is completely unreachable (not found), not if it's suspicious/phishing
            if (!$r1Validation || $r1Validation['status'] === 'not_found') {
                $errorMessage = $r1Validation['error'] ?? 'Domain not found or unreachable. Please check the URL and try again.';
                return [
                    'error' => $errorMessage,
                    'status' => 'error'
                ];
            }
            
            // Domain is valid (even if suspicious/phishing), now call the ML API
            $apiUrl = 'http://localhost:5000/predict';
            $postData = json_encode(['url' => $url]);
            
            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            if ($curlError) {
                error_log("ML API cURL error: " . $curlError);
                return [
                    'error' => 'ML API connection failed: ' . $curlError,
                    'status' => 'error'
                ];
            }
            
            if ($httpCode !== 200) {
                error_log("ML API HTTP error: " . $httpCode . " - Response: " . $response);
                return [
                    'error' => 'ML API error: HTTP ' . $httpCode,
                    'status' => 'error'
                ];
            }
            
            $result = json_decode($response, true);
            if (!$result || !isset($result['status']) || $result['status'] !== 'success') {
                error_log("ML API invalid response: " . $response);
                return [
                    'error' => 'ML API invalid response',
                    'status' => 'error'
                ];
            }
            
            // Return the ML API result
            return $result;
            
        } catch (\Exception $e) {
            error_log("ML API call failed: " . $e->getMessage());
            return [
                'error' => 'ML API call failed: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }
    
    private function getWhoisFromR1($url) {
        try {
            // Use the existing URLScan model to get WHOIS data
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) return [];
            
            // Get WHOIS data using the existing R1 system by calling scanURL
            // This will give us the complete scan result including WHOIS data
            $scanResult = $this->urlScan->scanURL($url, null, false, true); // testMode = true to skip domain reachability check
            
            if ($scanResult && isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])) {
                return $scanResult['whois_info'];
            }
            
            return [];
        } catch (\Exception $e) {
            error_log("Error getting WHOIS data: " . $e->getMessage());
            return [];
        }
    }
    
    private function autoAddToBlacklist($url) {
        try {
            $domain = parse_url($url, PHP_URL_HOST);
            if (!$domain) {
                error_log("Auto-blacklist: Invalid domain from URL: $url");
                return false;
            }
            
            error_log("Auto-blacklist: Attempting to add domain '$domain' to blacklist");
            
            // Check if domain is already blacklisted
            $existingDomain = $this->blacklist->getDomainByDomain($domain);
            if ($existingDomain) {
                error_log("Auto-blacklist: Domain '$domain' is already blacklisted");
                return true; // Already blacklisted, consider it successful
            }
            
            // Add domain to blacklist using the model directly
            $userId = $_SESSION['user_id'] ?? 1; // Default to admin user ID 1 if not set
            $reason = 'Auto-detected as phishing by ML model';
            
            $result = $this->blacklist->addDomain($domain, $reason, $userId);
            
            if ($result) {
                error_log("Auto-blacklist: Successfully added domain '$domain' to blacklist");
                
                // Update the scan result to show it was auto-blacklisted
                if (isset($_SESSION['scan_result'])) {
                    $_SESSION['scan_result']['auto_blacklisted'] = true;
                    $_SESSION['scan_result']['blacklist_message'] = "Domain automatically added to blacklist as phishing.";
                }
                
                return true;
            } else {
                error_log("Auto-blacklist: Failed to add domain '$domain' to blacklist");
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Error auto-adding to blacklist: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    // Get import history for reports
    private function getImportHistory($filters = []) {
        try {
            $db = \App\Config\Database::getDB();
            
            $whereConditions = [];
            $params = [];
            
            // Add domain filter
            if (isset($filters['domain']) && !empty($filters['domain'])) {
                $whereConditions[] = "ih.domain LIKE ?";
                $params[] = '%' . $filters['domain'] . '%';
            }
            
            // Add date filters
            if (isset($filters['date_from']) && !empty($filters['date_from'])) {
                $whereConditions[] = "ih.last_import_date >= ?";
                $params[] = $filters['date_from'] . ' 00:00:00';
            }
            
            if (isset($filters['date_to']) && !empty($filters['date_to'])) {
                $whereConditions[] = "ih.last_import_date <= ?";
                $params[] = $filters['date_to'] . ' 23:59:59';
            }
            
            // Add status filter
            if (isset($filters['status']) && !empty($filters['status'])) {
                if ($filters['status'] === 'blacklisted') {
                    $whereConditions[] = "ih.last_auto_blacklisted = 1";
                } else {
                    $whereConditions[] = "ih.last_scan_result = ?";
                    $params[] = $filters['status'];
                }
            }
            
            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
            
            $sql = "
                SELECT 
                    ih.*,
                    u.username as imported_by,
                    CASE 
                        WHEN ih.last_auto_blacklisted = 1 THEN 'Auto-Blacklisted'
                        WHEN ih.last_scan_result = 'phishing' THEN 'Phishing'
                        WHEN ih.last_scan_result = 'safe' THEN 'Safe'
                        ELSE 'Unknown'
                    END as display_status
                FROM import_history ih
                LEFT JOIN users u ON ih.user_id = u.id
                $whereClause
                ORDER BY ih.last_import_date DESC
                LIMIT 1000
            ";
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Error getting import history: " . $e->getMessage());
            return [];
        }
    }
} 