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
            switch ($action) {
                case 'update':
                    $this->user->updateUser($_POST['user_id'], [
                        'status' => $_POST['status'],
                        'role' => $_POST['role']
                    ]);
                    break;
                    
                case 'delete':
                    $this->user->deleteUser($_POST['user_id']);
                    break;
            }
        }
        
        $users = $this->user->getAllUsers();
        include __DIR__ . '/../views/admin/users.php';
    }
    
    public function reports() {
        try {
            $filters = [];
            if (isset($_GET['date_from']) && $_GET['date_from']) {
                $filters['date_from'] = $_GET['date_from'];
            }
            if (isset($_GET['date_to']) && $_GET['date_to']) {
                $filters['date_to'] = $_GET['date_to'];
            }
            if (isset($_GET['is_phishing']) && $_GET['is_phishing'] !== '') {
                $filters['is_phishing'] = $_GET['is_phishing'];
            }
            $reports = $this->urlScan->getFilteredScans($filters);
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
            $report = $this->urlScan->getScanById($id);
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
                                if ($blacklist->addDomain($domain, $reason, $_SESSION['user_id'])) {
                                    $_SESSION['success'] = "Domain added to blacklist successfully.";
                                } else {
                                    $_SESSION['error'] = "Failed to add domain to blacklist.";
                                }
                            }
                            break;
                            
                        case 'delete':
                            if (isset($_POST['domain_id'])) {
                                if ($blacklist->deleteDomain($_POST['domain_id'])) {
                                    $_SESSION['success'] = "Domain removed from blacklist successfully.";
                                } else {
                                    $_SESSION['error'] = "Failed to remove domain from blacklist.";
                                }
                            }
                            break;
                    }
                }
                header('Location: /url_phishing_project/public/admin/blacklist');
                exit;
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
            $_SESSION['error'] = "An error occurred while managing the blacklist.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }

    public function users() {
        $action = $_POST['action'] ?? '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                    $this->user->updateUser($_POST['user_id'], [
                        'status' => $_POST['status'],
                        'role' => $_POST['role']
                    ]);
                    break;
                case 'delete':
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
                if (!empty($url)) {
                    $urlScan = new URLScan();
                    $scanResult = $urlScan->scanURL($url, $_SESSION['user_id']);
                    
                    if ($scanResult === false) {
                        $error = "Failed to scan URL. Please try again.";
                    } else {
                        // Get WHOIS information
                        $domainInfo = new DomainInfo();
                        $whoisData = $domainInfo->getDomainInfo($url);
                        
                        if ($whoisData) {
                            $scanResult['whois'] = [
                                'Domain Age' => $whoisData['domain_age'] ?? 'Unknown',
                                'Domain Registered' => $whoisData['is_registered'] ? 'Yes' : 'No',
                                'Domain Expiry' => $whoisData['expiration_date'] ?? 'Unknown',
                                'Domain Status' => is_array($whoisData['status']) ? implode(', ', $whoisData['status']) : ($whoisData['status'] ?? 'Unknown'),
                                'Domain Registrar' => $whoisData['registrar'] ?? 'Unknown',
                                'Last Updated' => $whoisData['last_updated'] ?? 'Unknown',
                                'Domain Owner' => $whoisData['owner'] ?? 'Unknown',
                                'WHOIS Server' => $whoisData['whois_server'] ?? 'Unknown',
                                'Nameservers' => is_array($whoisData['nameservers']) ? implode(', ', $whoisData['nameservers']) : ($whoisData['nameservers'] ?? 'Unknown'),
                                'Domain ID' => $whoisData['domain_id'] ?? 'Unknown'
                            ];
                        }
                    }
                } else {
                    $error = "Please enter a valid URL.";
                }
            }
            require_once __DIR__ . '/../views/admin/scan.php';
        } catch (\Exception $e) {
            error_log("Error in admin scan: " . $e->getMessage());
            $_SESSION['error'] = "An error occurred while scanning the URL.";
            header('Location: /url_phishing_project/public/admin/dashboard');
            exit;
        }
    }
} 