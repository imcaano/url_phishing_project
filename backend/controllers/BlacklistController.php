<?php
namespace App\Controllers;

use App\Models\DomainBlacklist;

class BlacklistController {
    private $blacklist;
    
    public function __construct() {
        $this->blacklist = new DomainBlacklist();
    }
    
    public function index() {
        $blacklistedDomains = $this->blacklist->getAllDomains();
        require_once __DIR__ . '/../views/blacklist.php';
    }
    
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
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
        
        header('Location: /url_phishing_project/public/blacklist');
        exit;
    }
} 