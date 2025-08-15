<?php
namespace App\Controllers;

use App\Models\DomainBlacklist;

class BlacklistController {
    private $blacklist;
    
    public function __construct() {
        $this->blacklist = new DomainBlacklist();
    }
    
    public function index() {
        // Handle POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            
            switch ($action) {
                case 'user_add':
                    $this->add();
                    break;
                case 'delete':
                    $this->delete();
                    break;
                default:
                    header('Location: /url_phishing_project/public/blacklist');
                    exit;
            }
        }
        
        $blacklistedDomains = $this->blacklist->getAllDomains();
        require_once __DIR__ . '/../views/blacklist.php';
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        // Check if this is a user form submission (not admin)
        if (!isset($_POST['action']) || $_POST['action'] !== 'user_add') {
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        // Check if user is admin - only admins can add domains to blacklist
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Access denied. Only administrators can add domains to blacklist.";
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        $domain = filter_var($_POST['domain'], FILTER_SANITIZE_STRING);
        $reason = filter_var($_POST['reason'], FILTER_SANITIZE_STRING);
        $userId = $_SESSION['user_id'] ?? null;
        
        if (empty($domain) || empty($reason)) {
            $_SESSION['error'] = "Domain and reason are required.";
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        try {
            $this->blacklist->addDomain($domain, $reason, $userId);
            $_SESSION['success'] = "Domain has been added to blacklist successfully.";
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error adding domain: " . $e->getMessage();
        }
        
        // Redirect back to the same page (user blacklist, not admin)
        header('Location: /url_phishing_project/public/blacklist');
        exit;
    }
    
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        // Check if user is admin - only admins can delete domains
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['error'] = "Access denied. Only administrators can delete domains from blacklist.";
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        $domainId = $_POST['domain_id'] ?? null;
        
        if (empty($domainId)) {
            $_SESSION['error'] = "Domain ID is required.";
            header('Location: /url_phishing_project/public/blacklist');
            exit;
        }
        
        try {
            // Get domain info first
            $domainInfo = $this->blacklist->getDomainById($domainId);
            if (!$domainInfo) {
                $_SESSION['error'] = "Domain not found in blacklist.";
                header('Location: /url_phishing_project/public/blacklist');
                exit;
            }
            
            if ($this->blacklist->deleteDomain($domainId)) {
                $_SESSION['success'] = "Domain '{$domainInfo['domain']}' has been removed from blacklist successfully.";
            } else {
                $_SESSION['error'] = "Failed to remove domain from blacklist.";
            }
        } catch (\Exception $e) {
            $_SESSION['error'] = "Error deleting domain: " . $e->getMessage();
        }
        
        header('Location: /url_phishing_project/public/blacklist');
        exit;
    }

    public function addToBlacklist() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $domain = $_POST['domain'] ?? '';
            $reason = $_POST['reason'] ?? 'Added via scan';
            $customReason = $_POST['custom_reason'] ?? '';
            
            // Use custom reason if provided
            if (!empty($customReason)) {
                $reason = $customReason;
            }
            
            if (empty($domain)) {
                if ($this->isAjaxRequest()) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Domain is required']);
                    return;
                } else {
                    $_SESSION['error'] = 'Domain is required';
                    $this->redirectBack();
                    return;
                }
            }
            
            try {
                $pdo = $this->getDatabaseConnection();
                
                // Check if domain already exists
                $stmt = $pdo->prepare("SELECT id FROM domain_blacklist WHERE domain = ?");
                $stmt->execute([$domain]);
                
                if ($stmt->fetch()) {
                    if ($this->isAjaxRequest()) {
                        echo json_encode(['success' => false, 'message' => 'Domain is already in blacklist']);
                        return;
                    } else {
                        $_SESSION['error'] = 'Domain is already in blacklist';
                        $this->redirectBack();
                        return;
                    }
                }
                
                // Add to blacklist
                $stmt = $pdo->prepare("INSERT INTO domain_blacklist (domain, reason, added_by, added_date) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$domain, $reason, $_SESSION['user_id'] ?? 'admin']);
                
                if ($this->isAjaxRequest()) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Domain "' . htmlspecialchars($domain) . '" added to blacklist successfully'
                    ]);
                    return;
                } else {
                    $_SESSION['success'] = 'Domain "' . htmlspecialchars($domain) . '" added to blacklist successfully';
                    $this->redirectBack();
                }
                
            } catch (\Exception $e) {
                error_log("Blacklist add error: " . $e->getMessage());
                if ($this->isAjaxRequest()) {
                    http_response_code(500);
                    echo json_encode(['success' => false, 'message' => 'Failed to add domain to blacklist']);
                    return;
                } else {
                    $_SESSION['error'] = 'Failed to add domain to blacklist';
                    $this->redirectBack();
                }
            }
        }
    }
    
    private function redirectBack() {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/public/admin/blacklist';
        header('Location: ' . $referer);
        exit;
    }

    private function isAjaxRequest() {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }
} 