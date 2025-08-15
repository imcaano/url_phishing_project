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

        /* Sidebar Profile Styles */
        .sidebar-profile {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 1.5rem;
        }

        .profile-header {
            text-align: center;
            margin-bottom: 1rem;
        }

        .profile-avatar {
            margin-bottom: 0.5rem;
        }

        .profile-avatar img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-name {
            color: white;
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 0.9rem;
        }

        .profile-role {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            display: inline-block;
        }

        .profile-actions {
            margin-top: 1rem;
        }

        .profile-actions .nav-link {
            font-size: 0.85rem;
            padding: 0.6rem 0.8rem;
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
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters-form button:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Expert-size responsive button styles */
        .btn-edit {
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
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-delete {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
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

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
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
            
            <!-- Profile Section -->
            <div class="sidebar-profile mt-4">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff&size=60" alt="Profile Avatar">
                    </div>
                    <div class="profile-info">
                        <h6 class="profile-name"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></h6>
                        <span class="profile-role"><?php echo ucfirst($_SESSION['role'] ?? 'admin'); ?></span>
                    </div>
                </div>
                <div class="profile-actions">
                    <a href="/url_phishing_project/public/admin/profile" class="nav-link">
                        <i class="fas fa-user-cog"></i> Profile Settings
                    </a>
                    <a href="/url_phishing_project/public/logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
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

        <!-- Statistics Section -->
        <div class="stats-section" style="margin-bottom: 2rem;">
            <div class="row">

                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #e74a3b 0%, #c82333 100%); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3><?php echo $domainStats['phishing_domains'] ?? 0; ?></h3>
                        <p>Phishing Domains</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-shield-alt" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3><?php echo $domainStats['safe_domains'] ?? 0; ?></h3>
                        <p>Safe Domains</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #36b9cc 0%, #2a96a5 100%); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-chart-line" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3><?php echo $domainStats['total_scans'] ?? 0; ?></h3>
                        <p>Total Scans</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card" style="background: linear-gradient(135deg, #1cc88a 0%, #17a673 100%); color: white; padding: 1.5rem; border-radius: 15px; text-align: center; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 0.5rem;"></i>
                        <h3><?php echo $domainStats['total_users'] ?? 0; ?></h3>
                        <p>Total Users</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-section">

            <!-- Export Buttons -->
            <div class="export-section" style="margin-bottom: 1.5rem; text-align: right;">
                <button type="button" class="btn btn-success" onclick="exportToPDF()">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </button>
            </div>
            
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
                    <label><i class="fas fa-calendar me-1"></i>Date To:</label>
                    <input type="date" name="date_to" value="<?php echo $_GET['date_to'] ?? ''; ?>">
                </div>
                <div class="form-group">
                    <label><i class="fas fa-filter me-1"></i>Status:</label>
                    <select name="status">
                        <option value="">All Results</option>
                        <option value="phishing" <?php echo ($_GET['status'] ?? '') === 'phishing' ? 'selected' : ''; ?>>Phishing Detected</option>
                        <option value="safe" <?php echo ($_GET['status'] ?? '') === 'safe' ? 'selected' : ''; ?>>Safe Domains</option>
                    </select>
                </div>

                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <div class="table-container">
                <table class="reports-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-globe me-2"></i>Domain</th>
                            <th><i class="fas fa-calendar me-2"></i>First Scan</th>
                            <th><i class="fas fa-clock me-2"></i>Last Scan</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($scannedDomains)): ?>
                            <?php foreach ($scannedDomains as $domain): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($domain['domain']); ?></strong>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($domain['first_scan_date'])); ?></td>
                                <td><?php echo date('M j, Y H:i', strtotime($domain['last_scan_date'])); ?></td>
                                <td>
                                    <?php 
                                    // Show status based on blacklist and scan results
                                    if ($domain['is_blacklisted']) {
                                        $status = 'Blacklisted';
                                        $statusClass = 'status-phishing';
                                    } elseif ($domain['phishing_count'] > $domain['safe_count']) {
                                        $status = 'Phishing';
                                        $statusClass = 'status-phishing';
                                    } else {
                                        $status = 'Safe';
                                        $statusClass = 'status-safe';
                                    }
                                    ?>
                                    <span class="status-badge <?php echo $statusClass; ?>">
                                        <?php echo $status; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="/url_phishing_project/public/admin/report_details?domain=<?php echo urlencode($domain['domain']); ?>" class="btn-edit">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        <?php if ($domain['is_blacklisted']): ?>
                                            <button type="button" class="btn btn-warning btn-sm" onclick="backToSafe('<?php echo htmlspecialchars($domain['domain']); ?>', '<?php echo $status; ?>')">
                                                <i class="fas fa-undo"></i> Back to Safe
                                            </button>
                                        <?php endif; ?>
                                        <form action="/url_phishing_project/public/admin/reports" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="domain" value="<?php echo htmlspecialchars($domain['domain']); ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this domain? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5">
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

    <!-- Import History Section -->
    <div class="card mt-4">
        <div class="card-header">
            <h4><i class="fas fa-download"></i> Import History</h4>
            <p class="text-muted mb-0">Track domains imported via bulk import functionality</p>
        </div>
        <div class="card-body">
            <?php if (!empty($importHistory)): ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Domain</th>
                                <th>Imported By</th>
                                <th>First Import</th>
                                <th>Last Import</th>
                                <th>Import Count</th>
                                <th>Last ML Result</th>
                                <th>Last Confidence</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($importHistory as $import): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($import['domain']); ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?php echo htmlspecialchars($import['imported_by'] ?? 'Unknown'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y H:i', strtotime($import['first_import_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php echo date('M j, Y H:i', strtotime($import['last_import_date'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?php echo $import['import_count']; ?>x
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $import['last_ml_prediction'] === 'phishing' ? 'bg-danger' : ($import['last_ml_prediction'] === 'safe' ? 'bg-success' : 'bg-warning'); ?>">
                                            <?php echo ucfirst($import['last_ml_prediction']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($import['last_confidence_score'] > 0): ?>
                                            <span class="badge bg-info">
                                                <?php echo number_format($import['last_confidence_score'], 1); ?>%
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $import['display_status'] === 'Auto-Blacklisted' ? 'bg-warning' : ($import['display_status'] === 'Phishing' ? 'bg-danger' : 'bg-success'); ?>">
                                            <?php echo $import['display_status']; ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state text-center py-4">
                    <i class="fas fa-download fa-3x text-muted mb-3"></i>
                    <h5>No Import History Found</h5>
                    <p class="text-muted">Import history will appear here after you use the bulk import functionality.</p>
                    <a href="/url_phishing_project/public/admin/import" class="btn btn-primary">
                        <i class="fas fa-upload"></i> Go to Import Page
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    
    <script>
        function exportToPDF() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            // Add title
            doc.setFontSize(20);
            doc.text('URL Phishing Detection - Scan Reports', 20, 20);
            
            // Add date
            doc.setFontSize(12);
            doc.text('Generated on: ' + new Date().toLocaleDateString(), 20, 30);
            
            // Add statistics
            doc.setFontSize(14);
            doc.text('Statistics:', 20, 45);
            doc.setFontSize(12);
            doc.text('• Total Domains: <?php echo $domainStats['total_domains'] ?? 0; ?>', 25, 55);
            doc.text('• Phishing Domains: <?php echo $domainStats['phishing_domains'] ?? 0; ?>', 25, 65);
            doc.text('• Safe Domains: <?php echo $domainStats['safe_domains'] ?? 0; ?>', 25, 75);
            doc.text('• Total Scans: <?php echo $domainStats['total_scans'] ?? 0; ?>', 25, 85);
            
            // Prepare table data
            const tableData = [];
            <?php if (!empty($scannedDomains)): ?>
                <?php foreach ($scannedDomains as $domain): ?>
                    <?php 
                    if ($domain['is_blacklisted']) {
                        $status = 'Blacklisted';
                    } elseif ($domain['phishing_count'] > $domain['safe_count']) {
                        $status = 'Phishing';
                    } else {
                        $status = 'Safe';
                    }
                    ?>
                    tableData.push([
                        '<?php echo addslashes($domain['domain']); ?>',
                        '<?php echo date('M j, Y', strtotime($domain['first_scan_date'])); ?>',
                        '<?php echo date('M j, Y H:i', strtotime($domain['last_scan_date'])); ?>',
                        '<?php echo $status; ?>'
                    ]);
                <?php endforeach; ?>
            <?php endif; ?>
            
            // Add table
            doc.autoTable({
                head: [['Domain', 'First Scan', 'Last Scan', 'Status']],
                body: tableData,
                startY: 100,
                styles: {
                    fontSize: 10,
                    cellPadding: 5
                },
                headStyles: {
                    fillColor: [78, 115, 223],
                    textColor: 255
                },
                alternateRowStyles: {
                    fillColor: [248, 249, 252]
                }
            });
            
            // Save the PDF
            doc.save('phishing-detection-reports.pdf');
        }

        // Back to Safe function - Restore domain from blacklist and mark as safe
        function backToSafe(domain, currentStatus) {
            if (confirm('Are you sure you want to mark "' + domain + '" as safe? This will remove it from the blacklist if it\'s listed.')) {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
                button.disabled = true;

                // Use AJAX to restore domain without page redirect
                fetch('/url_phishing_project/public/admin/blacklist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=restore&domain=' + encodeURIComponent(domain)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Update the button to show it's been restored
                        button.innerHTML = '<i class="fas fa-check"></i> Restored';
                        button.className = 'btn btn-sm btn-success';
                        button.disabled = true;
                        
                        // Update the domain status in the results table
                        updateDomainStatus(domain, 'safe');
                    } else {
                        // Show error message
                        alert(data.message);
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to restore domain. Please try again.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        // Update domain status in the results table
        function updateDomainStatus(domain, newStatus) {
            // Find the domain row in the results table
            const rows = document.querySelectorAll('.reports-table tbody tr');
            rows.forEach(row => {
                const domainCell = row.querySelector('td:first-child strong');
                if (domainCell && domainCell.textContent.trim() === domain) {
                    // Update status badge
                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = newStatus;
                        statusBadge.className = 'status-badge status-safe';
                    }
                    
                    // Hide the Back to Safe button
                    const backToSafeBtn = row.querySelector('.btn-warning');
                    if (backToSafeBtn) {
                        backToSafeBtn.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html> 