<?php

class ReportController {
    private $db;

    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function getReports($filters = []) {
        try {
            $query = "SELECT 
                        id,
                        url,
                        scan_date,
                        risk_level,
                        'completed' as status,
                        expert_analysis as details,
                        is_phishing,
                        confidence_score,
                        domain
                    FROM url_scans
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filters['date_from'])) {
                $query .= " AND scan_date >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['risk_level'])) {
                $query .= " AND risk_level = :risk_level";
                $params[':risk_level'] = $filters['risk_level'];
            }
            
            $query .= " ORDER BY scan_date DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            
            return [
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    public function getReportDetails($scanId) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    id,
                    url,
                    domain,
                    scan_date,
                    risk_level,
                    'completed' as status,
                    expert_analysis as details,
                    is_phishing,
                    confidence_score,
                    features,
                    whois_info
                FROM url_scans
                WHERE id = :scan_id
            ");
            
            $stmt->execute([':scan_id' => $scanId]);
            $report = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$report) {
                throw new Exception("Report not found");
            }
            
            // Decode JSON fields
            if (isset($report['features'])) {
                $report['features'] = json_decode($report['features'], true);
            }
            if (isset($report['whois_info'])) {
                $report['whois_info'] = json_decode($report['whois_info'], true);
            }
            
            return [
                'status' => 'success',
                'data' => $report
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
} 