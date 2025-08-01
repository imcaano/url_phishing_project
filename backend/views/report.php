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
            <a href="/url_phishing_project/public/predict" class="nav-link">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/report" class="nav-link active">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="/url_phishing_project/public/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Blacklist
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

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo $domainStats['total_domains'] ?? 0; ?></div>
                <div class="stat-label">Total Domains Scanned</div>
            </div>
            <div class="stat-item danger">
                <div class="stat-number"><?php echo $domainStats['phishing_domains'] ?? 0; ?></div>
                <div class="stat-label">Phishing Domains</div>
            </div>
            <div class="stat-item warning">
                <div class="stat-number"><?php echo $domainStats['blacklisted_domains'] ?? 0; ?></div>
                <div class="stat-label">Blacklisted Domains</div>
            </div>
            <div class="stat-item success">
                <div class="stat-number"><?php echo $domainStats['total_scans'] ?? 0; ?></div>
                <div class="stat-label">Total Scans</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $domainStats['avg_confidence_score'] ?? 0; ?>%</div>
                <div class="stat-label">Avg Confidence Score</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-tabs" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="domains-tab" data-bs-toggle="tab" data-bs-target="#domains" type="button" role="tab">
                    <i class="fas fa-globe"></i> Scanned Domains
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="scans-tab" data-bs-toggle="tab" data-bs-target="#scans" type="button" role="tab">
                    <i class="fas fa-history"></i> Individual Scans
                </button>
            </li>
        </ul>

        <div class="tab-content" id="reportTabsContent">
            <!-- Scanned Domains Tab -->
            <div class="tab-pane fade show active" id="domains" role="tabpanel">
                <!-- Filter Section -->
                <div class="filter-section">
                    <h5><i class="fas fa-filter"></i> Filter Domains</h5>
                    <form method="GET" action="/url_phishing_project/public/report" class="row g-3">
                        <div class="col-md-3">
                            <label for="domain" class="form-label">Domain</label>
                            <input type="text" class="form-control" id="domain" name="domain" 
                                   value="<?php echo htmlspecialchars($_GET['domain'] ?? ''); ?>" placeholder="Search domain...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="phishing" <?php echo ($_GET['status'] ?? '') === 'phishing' ? 'selected' : ''; ?>>Phishing</option>
                                <option value="safe" <?php echo ($_GET['status'] ?? '') === 'safe' ? 'selected' : ''; ?>>Safe</option>
                                <option value="blacklisted" <?php echo ($_GET['status'] ?? '') === 'blacklisted' ? 'selected' : ''; ?>>Blacklisted</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="risk_level" class="form-label">Risk Level</label>
                            <select class="form-select" id="risk_level" name="risk_level">
                                <option value="">All</option>
                                <option value="high" <?php echo ($_GET['risk_level'] ?? '') === 'high' ? 'selected' : ''; ?>>High</option>
                                <option value="medium" <?php echo ($_GET['risk_level'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium</option>
                                <option value="low" <?php echo ($_GET['risk_level'] ?? '') === 'low' ? 'selected' : ''; ?>>Low</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="date_from" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="date_from" name="date_from" 
                                   value="<?php echo htmlspecialchars($_GET['date_from'] ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                                <a href="/url_phishing_project/public/report" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Domains Table -->
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>First Scan</th>
                                <th>Last Scan</th>
                                <th>Total Scans</th>
                                <th>Phishing Count</th>
                                <th>Risk Level</th>
                                <th>Domain Age</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($scannedDomains)): ?>
                                <?php foreach ($scannedDomains as $domain): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($domain['domain']); ?></strong>
                                        <?php if ($domain['is_blacklisted']): ?>
                                            <span class="badge bg-danger ms-2">Blacklisted</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M j, Y', strtotime($domain['first_scan_date'])); ?></td>
                                    <td><?php echo date('M j, Y H:i', strtotime($domain['last_scan_date'])); ?></td>
                                    <td><?php echo $domain['total_scans']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $domain['phishing_count'] > 0 ? 'danger' : 'success'; ?>">
                                            <?php echo $domain['phishing_count']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-risk <?php echo strtolower($domain['risk_level']); ?>">
                                            <?php echo $domain['risk_level']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($domain['domain_age']); ?></td>
                                    <td>
                                        <?php if ($domain['is_blacklisted']): ?>
                                            <span class="badge bg-danger">Blacklisted</span>
                                        <?php elseif ($domain['phishing_count'] > 0): ?>
                                            <span class="badge bg-warning">Suspicious</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Safe</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>No scanned domains found. Start scanning URLs to see domains here.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Individual Scans Tab -->
            <div class="tab-pane fade" id="scans" role="tabpanel">
                <div class="table-container">
                    <table class="table">
                <thead>
                    <tr>
                        <th>URL</th>
                                <th>Scan Date</th>
                                <th>Result</th>
                                <th>Confidence</th>
                                <th>Risk Level</th>
                    </tr>
                </thead>
                <tbody>
                            <?php if (!empty($userScans)): ?>
                                <?php foreach ($userScans as $scan): ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo htmlspecialchars($scan['url']); ?>" target="_blank" class="text-decoration-none">
                                            <?php echo htmlspecialchars($scan['url']); ?>
                                        </a>
                                    </td>
                                    <td><?php echo date('M j, Y H:i', strtotime($scan['scan_date'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $scan['is_phishing'] ? 'danger' : 'success'; ?>">
                                <?php echo $scan['is_phishing'] ? 'Phishing' : 'Safe'; ?>
                            </span>
                        </td>
                                    <td><?php echo number_format($scan['confidence_score'], 1); ?>%</td>
                                    <td>
                                        <span class="badge-risk <?php echo strtolower($scan['risk_level']); ?>">
                                            <?php echo $scan['risk_level']; ?>
                                        </span>
                                    </td>
                    </tr>
                    <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="fas fa-history fa-2x mb-3"></i>
                                        <p>No scan history found. Start scanning URLs to see your scan history here.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                </tbody>
            </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 