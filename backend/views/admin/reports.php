<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Reports - Admin</title>
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
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .filters-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--light-color) 0%, #ffffff 100%);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-light);
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            flex: 1;
        }

        .form-group label {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-group input,
        .form-group select {
            padding: 0.75rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .filters-form button {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            min-width: 120px;
        }

        .filters-form button:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Expert Table Design */
        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--shadow-medium);
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .reports-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0;
        }

        .reports-table th {
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

        .reports-table th:first-child {
            border-top-left-radius: 15px;
        }

        .reports-table th:last-child {
            border-top-right-radius: 15px;
        }

        .reports-table td {
            padding: 1.25rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .reports-table tr {
            transition: all 0.3s ease;
        }

        .reports-table tr:hover {
            background: linear-gradient(135deg, #f8f9fc 0%, #e8f2ff 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .reports-table tr:last-child td {
            border-bottom: none;
        }

        /* Consistent Button Design */
        .btn-view {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Status Styling */
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
            .filters-form {
                flex-direction: column;
                gap: 0.75rem;
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
            .reports-table {
                font-size: 0.85rem;
            }
            .reports-table th,
            .reports-table td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* URL Truncation */
        .url-cell {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .url-cell:hover {
            white-space: normal;
            word-break: break-all;
        }

        /* Migration Alert */
        .migration-alert {
            background: linear-gradient(135deg, var(--info-color) 0%, #2a9d8f 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .migration-alert .btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .migration-alert .btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
            color: white;
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
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link active">
                <i class="fas fa-file-alt"></i> Scan Reports
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
            <h2><i class="fas fa-file-alt me-2"></i>Scan Reports & Analytics</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Migration Alert -->
        <?php if (($domainStats['total_domains'] ?? 0) === 0): ?>
        <div class="migration-alert">
            <i class="fas fa-database me-2"></i>
            <strong>No scanned domains found.</strong> 
            If you have existing scan data, you can migrate it to the new system.
            <a href="/url_phishing_project/public/admin/reports?migrate=true" class="btn ms-3">
                <i class="fas fa-database"></i> Migrate Existing Data
            </a>
        </div>
        <?php endif; ?>

        <div class="admin-section">
            <form class="filters-form" method="GET">
                <div class="form-group">
                    <label><i class="fas fa-globe me-1"></i>Domain:</label>
                    <input type="text" name="domain" value="<?php echo htmlspecialchars($_GET['domain'] ?? ''); ?>" placeholder="Search domain...">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-calendar me-1"></i>Date From:</label>
                    <input type="date" name="date_from" value="<?php echo $_GET['date_from'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-filter me-1"></i>Status:</label>
                    <select name="status">
                        <option value="">All Results</option>
                        <option value="phishing" <?php echo ($_GET['status'] ?? '') === 'phishing' ? 'selected' : ''; ?>>Phishing</option>
                        <option value="safe" <?php echo ($_GET['status'] ?? '') === 'safe' ? 'selected' : ''; ?>>Safe</option>
                    </select>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-exclamation-triangle me-1"></i>Risk Level:</label>
                    <select name="risk_level">
                        <option value="">All Levels</option>
                        <option value="high" <?php echo ($_GET['risk_level'] ?? '') === 'high' ? 'selected' : ''; ?>>High Risk</option>
                        <option value="medium" <?php echo ($_GET['risk_level'] ?? '') === 'medium' ? 'selected' : ''; ?>>Medium Risk</option>
                        <option value="low" <?php echo ($_GET['risk_level'] ?? '') === 'low' ? 'selected' : ''; ?>>Low Risk</option>
                    </select>
                </div>
                <button type="submit">
                    <i class="fas fa-search me-1"></i> Filter
                </button>
            </form>

            <div class="table-container">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-globe me-2"></i>Domain</th>
                            <th><i class="fas fa-calendar me-2"></i>First Scan</th>
                            <th><i class="fas fa-clock me-2"></i>Last Scan</th>
                            <th><i class="fas fa-shield-alt me-2"></i>Risk Level</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-eye me-2"></i>View</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($scannedDomains)): ?>
                            <?php foreach ($scannedDomains as $domain): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($domain['domain']); ?></strong>
                                    <?php if ($domain['is_blacklisted']): ?>
                                        <span class="status-badge status-phishing ms-2">Blacklisted</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($domain['first_scan_date'])); ?></td>
                                <td><?php echo date('M j, Y H:i', strtotime($domain['last_scan_date'])); ?></td>
                                <td>
                                    <?php 
                                    $riskClass = strtolower($domain['risk_level']) === 'high' ? 'confidence-high' : 
                                                (strtolower($domain['risk_level']) === 'medium' ? 'confidence-medium' : 'confidence-low');
                                    ?>
                                    <span class="confidence-score <?php echo $riskClass; ?>">
                                        <?php echo $domain['risk_level']; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($domain['is_blacklisted']): ?>
                                        <span class="status-badge status-phishing">Blacklisted</span>
                                    <?php elseif ($domain['phishing_count'] > 0): ?>
                                        <span class="status-badge status-phishing">Phishing</span>
                                    <?php else: ?>
                                        <span class="status-badge status-safe">Safe</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/url_phishing_project/public/admin/report/<?php echo $domain['id']; ?>" class="btn-view">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-inbox"></i>
                                        <h4>No Scanned Domains Found</h4>
                                        <p>No scanned domains match your current filters. Try adjusting your search criteria or scan some URLs first.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 