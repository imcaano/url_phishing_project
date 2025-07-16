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
                        scans.id,
                        scans.url,
                        scans.scan_date,
                        scans.risk_level,
                        scans.status,
                        scan_results.details
                    FROM scans
                    LEFT JOIN scan_results ON scans.id = scan_results.scan_id
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
                    scans.*,
                    scan_results.details,
                    scan_results.raw_data
                FROM scans
                LEFT JOIN scan_results ON scans.id = scan_results.scan_id
                WHERE scans.id = :scan_id
            ");
            
            $stmt->execute([':scan_id' => $scanId]);
            $report = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$report) {
                throw new Exception("Report not found");
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