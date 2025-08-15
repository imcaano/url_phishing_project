<?php
// admin/report_details.php
if (!isset($report)) {
    echo '<div class="alert alert-danger">Report not found.</div>';
    return;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Report Details - Admin</title>
    <link rel="icon" type="image/png" href="/url_phishing_project/public/assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --border-color: #e3e6f0;
            --shadow-light: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --shadow-medium: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.25);
        }

        body {
            background: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            padding: 1.5rem;
            color: white;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            margin: 0;
            color: white;
            font-weight: 600;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            margin: 0.2rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 500;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .nav-link i {
            width: 25px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .top-navbar h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
            font-weight: 500;
            color: var(--dark-color);
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .admin-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            max-width: 800px;
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--secondary-color) 0%, #6c757d 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .back-link:hover {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: var(--shadow-light);
            border: 1px solid var(--border-color);
        }

        .details-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1rem;
            text-align: left;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            width: 200px;
        }

        .details-table td {
            padding: 1.25rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .details-table tr:hover {
            background: linear-gradient(135deg, #f8f9fc 0%, #e8f2ff 100%);
        }

        .status-badge {
            padding: 0.6rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .status-phishing { 
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%); 
            color: white; 
        }
        
        .status-safe { 
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%); 
            color: white; 
        }

        .confidence-score {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .confidence-high { color: var(--danger-color); }
        .confidence-medium { color: var(--warning-color); }
        .confidence-low { color: var(--success-color); }

        /* Action Buttons Section */
        .action-buttons-section {
            margin-top: 2rem;
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .action-buttons-section h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 1.2rem;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-blacklist, .btn-rescan, .btn-back {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-blacklist {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }

        .btn-blacklist:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-rescan {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
            color: white;
        }

        .btn-rescan:hover {
            background: linear-gradient(135deg, #e0a800 0%, #c69500 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-back {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #6c757d 100%);
            color: white;
        }

        .btn-back:hover {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .json-features-table {
            width: 100%;
            background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
            border-radius: 10px;
            padding: 1.5rem;
            font-size: 0.95rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
        }

        .json-features-table th, .json-features-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .json-features-table th {
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .json-features-table tr:last-child td {
            border-bottom: none;
        }

        .json-features-table tr:hover {
            background: rgba(78, 115, 223, 0.05);
        }

        .url-display {
            word-break: break-all;
            font-family: 'Courier New', monospace;
            background: var(--light-color);
            padding: 0.5rem;
            border-radius: 5px;
            border: 1px solid var(--border-color);
        }

        .json-data {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            max-height: 400px;
            overflow-y: auto;
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                padding: 1rem;
                margin-bottom: 1rem;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        @media (max-width: 767px) {
            .top-navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem;
            }
            .user-info {
                flex-direction: row;
                gap: 0.5rem;
            }
            .admin-section {
                padding: 1rem;
            }
            .details-table th,
            .details-table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
            .details-table th {
                width: 120px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt me-2"></i>Admin Panel</h1>
        </div>
        <nav>
            <a href="/url_phishing_project/public/admin/dashboard" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/admin/scan" class="nav-link">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link active">
                <i class="fas fa-file-alt"></i> Scan Reports
            </a>
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/import" class="nav-link">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h2><i class="fas fa-file-alt me-2"></i>Scan Report Details</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <h2><i class="fas fa-globe me-2"></i>Domain Information: <?php echo htmlspecialchars($report['domain']); ?></h2>
            
            <table class="details-table">
                <tr>
                    <th><i class="fas fa-globe me-2"></i>Domain</th>
                    <td><strong><?php echo htmlspecialchars($report['domain']); ?></strong></td>
                </tr>
                <tr>
                    <th><i class="fas fa-shield-alt me-2"></i>Security Status</th>
                    <td>
                        <?php 
                        $isPhishing = $report['is_blacklisted'] || $report['phishing_count'] > 0;
                        ?>
                        <span class="status-badge <?php echo $isPhishing ? 'status-phishing' : 'status-safe'; ?>">
                            <i class="fas fa-<?php echo $isPhishing ? 'exclamation-triangle' : 'check-circle'; ?> me-1"></i>
                            <?php echo $isPhishing ? 'Phishing' : 'Safe'; ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-calendar me-2"></i>First Scan</th>
                    <td>
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo date('F d, Y', strtotime($report['first_scan_date'])); ?>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-clock me-2"></i>Last Scan</th>
                    <td>
                        <i class="fas fa-calendar-alt me-1"></i>
                        <?php echo date('F d, Y H:i', strtotime($report['last_scan_date'])); ?>
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-chart-bar me-2"></i>Total Scans</th>
                    <td>
                        <i class="fas fa-search me-1"></i>
                        <?php echo $report['total_scans']; ?> scans
                    </td>
                </tr>
                <tr>
                    <th><i class="fas fa-exclamation-triangle me-2"></i>Phishing Detections</th>
                    <td>
                        <i class="fas fa-shield-alt me-1"></i>
                        <?php echo $report['phishing_count']; ?> phishing detections
                    </td>
                </tr>
                <?php if ($report['is_blacklisted']): ?>
                <tr>
                    <th><i class="fas fa-ban me-2"></i>Blacklist Status</th>
                    <td>
                        <span class="status-badge status-phishing">
                            <i class="fas fa-ban me-1"></i> Blacklisted
                        </span>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($report['scan_features'])): ?>
                <tr>
                    <th><i class="fas fa-cogs me-2"></i>Scan Features</th>
                    <td>
                        <?php
                        $features = json_decode($report['scan_features'], true);
                        if (is_array($features) && !empty($features)) {
                            echo '<table class="json-features-table">';
                            foreach ($features as $key => $value) {
                                $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : htmlspecialchars($value);
                                echo '<tr><th>' . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . '</th><td>' . $displayValue . '</td></tr>';
                            }
                            echo '</table>';
                        } else {
                            echo '<div class="json-data">' . htmlspecialchars($report['scan_features']) . '</div>';
                        }
                        ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($report['details'])): ?>
                <tr>
                    <th><i class="fas fa-info-circle me-2"></i>Additional Details</th>
                    <td>
                        <div class="json-data"><?php echo htmlspecialchars($report['details']); ?></div>
                    </td>
                </tr>
                <?php endif; ?>
            </table>

            <!-- Back Button -->
            <div class="mt-4 text-center">
                <a href="/url_phishing_project/public/admin/reports" class="btn btn-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Reports
                </a>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 
</html> 