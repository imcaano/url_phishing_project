<?php

namespace App\Controllers;

use PDO;
use App\Config\Database;

class DashboardController {
    private $db;

    public function __construct() {
        try {
            $this->db = Database::getDB();
        } catch (\Exception $e) {
            error_log("Database connection error: " . $e->getMessage());
            die("Could not connect to the database. Please check your configuration.");
        }
    }

    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
        try {
            // Get statistics
            $stats = $this->getStats();
            // Get recent scans
            $recentScans = $this->getRecentScans();
            // Get user country using IP geolocation
            $country = 'Unknown';
            if (!empty($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
                $geo = @file_get_contents("https://ipinfo.io/{$ip}/json");
                if ($geo) {
                    $geoData = json_decode($geo, true);
                    if (!empty($geoData['country'])) {
                        $country = $geoData['country'];
                    }
                }
            }
            $data = [
                'stats' => $stats,
                'recentScans' => $recentScans,
                'country' => $country
            ];
            extract($data);
            require_once __DIR__ . '/../views/dashboard.php';
        } catch (\Exception $e) {
            error_log("Dashboard error: " . $e->getMessage());
            echo "An error occurred while loading the dashboard. Please try again later.";
        }
    }

    private function getStats() {
        try {
            // Get total scans
            $totalScans = $this->db->query("SELECT COUNT(*) FROM url_scans")->fetchColumn();
            
            // Get phishing URLs count
            $phishingCount = $this->db->query("SELECT COUNT(*) FROM url_scans WHERE is_phishing = 1")->fetchColumn();
            
            // Calculate safe URLs
            $safeCount = $totalScans - $phishingCount;
            
            // Calculate percentages
            $phishingPercent = $totalScans > 0 ? round(($phishingCount / $totalScans) * 100, 1) : 0;
            $safePercent = $totalScans > 0 ? round(($safeCount / $totalScans) * 100, 1) : 0;

            return [
                'total_scans' => $totalScans,
                'phishing_detected' => $phishingCount,
                'safe_urls' => $safeCount,
                'phishing_percent' => $phishingPercent,
                'safe_percent' => $safePercent
            ];
        } catch (\Exception $e) {
            error_log("Error getting stats: " . $e->getMessage());
            return [
                'total_scans' => 0,
                'phishing_detected' => 0,
                'safe_urls' => 0,
                'phishing_percent' => 0,
                'safe_percent' => 0
            ];
        }
    }

    private function getRecentScans() {
        try {
            $userId = $_SESSION['user_id'];
            $role = $_SESSION['role'] ?? 'user';
            if ($role === 'admin') {
                $stmt = $this->db->query("
                    SELECT url, is_phishing, confidence_score, scan_date 
                    FROM url_scans 
                    ORDER BY scan_date DESC 
                    LIMIT 10
                ");
            } else {
                $stmt = $this->db->prepare("
                    SELECT url, is_phishing, confidence_score, scan_date 
                    FROM url_scans 
                    WHERE user_id = ?
                    ORDER BY scan_date DESC 
                    LIMIT 10
                ");
                $stmt->execute([$userId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting recent scans: " . $e->getMessage());
            return [];
        }
    }
} 