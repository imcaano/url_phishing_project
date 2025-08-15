<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /url_phishing_project/public/login');
    exit;
}
if ($_SESSION['role'] !== 'user') {
    header('Location: /url_phishing_project/public/admin/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Reports - URL Phishing Detection</title>
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
        }

        body {
            background: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-link i {
            width: 25px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-radius: 10px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            border-left: 4px solid var(--primary-color);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-left: 4px solid var(--info-color);
        }

        .stat-item.danger {
            border-left-color: var(--danger-color);
        }

        .stat-item.success {
            border-left-color: var(--success-color);
        }

        .stat-item.warning {
            border-left-color: var(--warning-color);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .filter-section {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: var(--light-color);
            border: none;
            padding: 1rem;
            font-weight: 600;
            color: var(--dark-color);
        }

        .table td {
            padding: 1rem;
            border: none;
            border-bottom: 1px solid #e3e6f0;
        }

        .badge-risk {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-risk.high {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-risk.medium {
            background: #fff3cd;
            color: #856404;
        }

        .badge-risk.low {
            background: #d1ecf1;
            color: #0c5460;
        }

        .nav-tabs {
            border-bottom: 2px solid #e3e6f0;
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            color: var(--secondary-color);
            padding: 1rem 2rem;
            font-weight: 500;
        }

        .nav-tabs .nav-link.active {
            color: var(--primary-color);
            background: none;
            border-bottom: 2px solid var(--primary-color);
        }

        .tab-content {
            padding-top: 1rem;
        }

        /* Table Styling */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow: hidden;
            border: 1px solid #e3e6f0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0;
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1rem;
            text-align: left;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
            transition: background-color 0.3s ease;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge.bg-danger {
            background: linear-gradient(135deg, #e74a3b 0%, #c82333 100%) !important;
            color: white;
            box-shadow: 0 2px 4px rgba(231, 74, 59, 0.3);
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%) !important;
            color: white;
            box-shadow: 0 2px 4px rgba(28, 200, 138, 0.3);
        }

        .text-muted {
            color: #858796 !important;
        }

        .fa-2x {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #858796;
        }

        /* Statistics Cards */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            font-size: 0.9rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .fw-bold {
            font-weight: 700 !important;
        }

        .bg-info {
            background: linear-gradient(135deg, #36b9cc 0%, #2a96a5 100%) !important;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt"></i> URL Scanner</h1>
        </div>
        <nav>
            <a href="/url_phishing_project/public/dashboard" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/scan" class="nav-link">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/report" class="nav-link active">
                <i class="fas fa-chart-bar"></i> Reports
            </a>

            <a href="/url_phishing_project/public/profile" class="nav-link">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <h2><i class="fas fa-chart-bar"></i> Scan Reports</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
            </div>
        </div>

        <!-- Scan Results Table -->
        <div class="table-container">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Domain Name</th>
                        <th>ML Result</th>
                        <th>Confidence Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($userScans)): ?>
                        <?php foreach ($userScans as $scan): ?>
                        <tr>
                            <td>
                                <strong><?php echo htmlspecialchars(parse_url($scan['url'], PHP_URL_HOST)); ?></strong>
                            </td>
                            <td>
                                <?php 
                                $isPhishing = $scan['is_phishing'] == 1;
                                $badgeClass = $isPhishing ? 'bg-danger' : 'bg-success';
                                $badgeText = $isPhishing ? 'Phishing' : 'Safe';
                                ?>
                                <span class="badge <?php echo $badgeClass; ?>"><?php echo $badgeText; ?></span>
                            </td>
                            <td>
                                <?php 
                                $confidence = $scan['confidence_score'] ?? 0;
                                // Green for safe, red for phishing
                                $confidenceColor = $isPhishing ? 'text-danger' : 'text-success';
                                ?>
                                <span class="<?php echo $confidenceColor; ?> fw-bold">
                                    <?php echo number_format($confidence, 1); ?>%
                                </span>
                            </td>

                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>No scans found. Start scanning URLs to see your ML-based scan reports here.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- User Statistics -->
        <?php if (!empty($userStats)): ?>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Scans</h5>
                        <h3><?php echo $userStats['total_scans']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Phishing Detected</h5>
                        <h3><?php echo $userStats['phishing_scans']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Safe URLs</h5>
                        <h3><?php echo $userStats['safe_scans']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h5 class="card-title">Avg Confidence</h5>
                        <h3><?php echo number_format($userStats['avg_confidence'], 1); ?>%</h3>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 