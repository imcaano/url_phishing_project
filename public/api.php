<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../backend/controllers/FileController.php';
require_once __DIR__ . '/../backend/controllers/ReportController.php';

use App\Controllers\APIController;
use App\Models\APIKey;

header('Content-Type: application/json');

// Validate API key
$api_key = $_SERVER['HTTP_X_API_KEY'] ?? null;
if (!$api_key) {
    http_response_code(401);
    echo json_encode(['error' => 'API key required']);
    exit;
}

$api = new APIController();
$fileController = new FileController();

// Basic API routing
$route = $_GET['route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($route) {
    case 'scan':
        $api->scanURL();
        break;
        
    case 'history':
        $api->getScanHistory();
        break;
        
    case '/api/file/read':
        if ($method === 'POST') {
            $response = $fileController->readFile($_POST);
            echo json_encode($response);
            exit;
        }
        break;
        
    case '/api/file/write':
        if ($method === 'POST') {
            $response = $fileController->writeFile($_POST);
            echo json_encode($response);
            exit;
        }
        break;
        
    case 'reports':
        if ($method === 'GET') {
            $reportController = new ReportController();
            $filters = [
                'date_from' => $_GET['date_from'] ?? null,
                'risk_level' => $_GET['risk_level'] ?? null
            ];
            $response = $reportController->getReports($filters);
            echo json_encode($response);
            exit;
        }
        break;
        
    case (preg_match('/^reports\/(\d+)$/', $route, $matches) ? true : false):
        if ($method === 'GET') {
            $reportController = new ReportController();
            $scanId = $matches[1];
            $response = $reportController->getReportDetails($scanId);
            echo json_encode($response);
            exit;
        }
        break;
        
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
} 