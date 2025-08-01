<?php
session_start();

// Require the Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Use statements
use App\Controllers\AuthController;
use App\Controllers\URLScanController;
use App\Controllers\AdminController;
use App\Controllers\UserController;
use App\Controllers\DashboardController;
use App\Controllers\BlacklistController;
use App\Controllers\ImportController;
use App\Controllers\ProfileController;
use App\Controllers\ScanController;

// Manually require the needed files
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'Database.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'AuthController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'URLScanController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'AdminController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'UserController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'DashboardController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'BlacklistController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ImportController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ProfileController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'ScanController.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'User.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'URLScan.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'APIKey.php';
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'DomainInfo.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the current route
$route = $_GET['route'] ?? '';

// Handle /admin/report/{id} for view details BEFORE the switch
if (preg_match('#^admin/report/(\d+)$#', $route, $matches)) {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /url_phishing_project/public/login');
        exit;
    }
    require_once __DIR__ . '/../backend/controllers/AdminController.php';
    $controller = new AdminController();
    $controller->reportDetails($matches[1]);
    exit;
}

// Basic routing
switch ($route) {
    case '':
    case 'home':
        if (isset($_SESSION['user_id'])) {
            $dashboard = new DashboardController();
            $dashboard->index();
        } else {
            include __DIR__ . '/../backend/views/home.php';
        }
        break;

    case 'login':
        $auth = new AuthController();
        $auth->login();
        break;

    case 'register':
        $auth = new AuthController();
        $auth->register();
        break;

    case 'dashboard':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        require_once __DIR__ . '/../backend/controllers/DashboardController.php';
        $controller = new DashboardController();
        $controller->index();
        break;

    case 'predict':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        $urlScan = new URLScanController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $scanResult = $urlScan->scan();
        }
        
        include __DIR__ . '/../backend/views/predict.php';
        break;

    case 'report':
    case 'reports':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        
        $urlScan = new URLScanController();
        
        // Get filter parameters
        $filters = [];
        if (isset($_GET['domain'])) $filters['domain'] = $_GET['domain'];
        if (isset($_GET['date_from'])) $filters['date_from'] = $_GET['date_from'];
        if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
        if (isset($_GET['risk_level'])) $filters['risk_level'] = $_GET['risk_level'];
        
        // Get scanned domains and stats
        $scannedDomains = $urlScan->getScannedDomains($filters);
        $domainStats = $urlScan->getScannedDomainStats();
        
        // Get individual user scans
        $userScans = $urlScan->getReports(['user_id' => $_SESSION['user_id']]);
        
        require_once __DIR__ . '/../backend/views/report.php';
        break;

    case 'logout':
        // Destroy session and clear session cookie
        session_unset();
        session_destroy();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        header('Location: /url_phishing_project/public/');
        exit;
        break;

    case 'blacklist':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        $blacklist = new BlacklistController();
        $blacklist->index();
        break;

    case 'blacklist/add':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        $blacklist = new BlacklistController();
        $blacklist->add();
        break;

    case 'import':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        $import = new ImportController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $result = $import->importFromExcel();
            $success = $result['success'];
            $message = $result['message'];
        }
        
        $import->index();
        break;

    case 'profile':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        $profile = new ProfileController();
        $profile->index();
        break;

    case 'profile/update':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        $profile = new ProfileController();
        $profile->update();
        break;

    case 'admin/dashboard':
    case 'admin/users':
    case 'admin/reports':
    case 'admin/blacklist':
    case 'admin/scan':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        require_once __DIR__ . '/../backend/controllers/AdminController.php';
        $controller = new AdminController();
        
        switch ($route) {
            case 'admin/dashboard':
                $controller->dashboard();
                break;
            case 'admin/users':
                $controller->users();
                break;
            case 'admin/reports':
                $controller->reports();
                break;
            case 'admin/blacklist':
                $controller->blacklist();
                break;
            case 'admin/scan':
                $controller->scan();
                break;
        }
        break;

    case 'admin/import':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        $import = new ImportController();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
            $result = $import->importDomains($_FILES['file']);
            $success = $result['success'];
            $message = $result['message'];
        }
        
        $import->index();
        break;

    case 'admin/profile':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        $profile = new ProfileController();
        $profile->adminIndex();
        break;

    case 'admin/profile/update':
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/dashboard');
            exit;
        }
        
        $profile = new ProfileController();
        $profile->adminUpdate();
        break;

    case 'scan':
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        
        $scan = new ScanController();
        $scan->index();
        break;

    default:
        include __DIR__ . '/../backend/views/404.php';
        break;
} 