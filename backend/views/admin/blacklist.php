<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Blacklist - Admin</title>
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

        /* Expert Table Design */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .blacklist-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0;
        }

        .blacklist-table th {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1rem;
            text-align: left;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .blacklist-table th:first-child {
            border-top-left-radius: 15px;
        }

        .blacklist-table th:last-child {
            border-top-right-radius: 15px;
        }

        .blacklist-table td {
            padding: 1.25rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .blacklist-table tr {
            transition: all 0.3s ease;
        }

        .blacklist-table tr:hover {
            background: linear-gradient(135deg, #f8f9fc 0%, #ffe8e8 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .blacklist-table tr:last-child td {
            border-bottom: none;
        }

        /* Consistent Button Design */
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

        .btn-add {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-import {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%); 
            color: white; 
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-import:hover {
            background: linear-gradient(135deg, #e0a800 0%, #c69500 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white; 
            text-decoration: none;
        }

        /* Alert Styling */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            box-shadow: var(--shadow-light);
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }

        /* Form Styling */
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--danger-color);
            box-shadow: 0 0 0 0.2rem rgba(231, 74, 59, 0.25);
        }

        /* Card Styling */
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1rem;
            border-radius: 15px;
        }

        .card-header {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
            font-weight: 600;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
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
            .blacklist-table {
                font-size: 0.85rem;
            }
            .blacklist-table th,
            .blacklist-table td {
                padding: 0.75rem 0.5rem;
            }
            .btn-delete {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
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

        /* Action Buttons Container */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
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
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link active">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
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
            <h2><i class="fas fa-ban me-2"></i>Domain Blacklist Management</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Debug Information -->
            <?php if (empty($domains)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Debug Info:</strong> No domains found in blacklist. 
                    Total domains in database: <?php echo count($domains ?? []); ?>
                </div>
            <?php endif; ?>



            <!-- Blacklist Table -->
            <div class="table-container">
                <table class="blacklist-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-globe me-2"></i>Domain</th>
                            <th><i class="fas fa-user me-2"></i>Added By</th>
                            <th><i class="fas fa-calendar me-2"></i>Added Date</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($domains)): ?>
                            <?php foreach ($domains as $entry): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($entry['domain']); ?></strong>
                                </td>
                                <td>
                                    <i class="fas fa-user me-1"></i>
                                    <?php echo htmlspecialchars($entry['added_by'] ?? 'Unknown'); ?>
                                </td>
                                <td>
                                        <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo htmlspecialchars($entry['added_at']); ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-delete" 
                                                onclick="deleteDomain(<?php echo $entry['id']; ?>, '<?php echo htmlspecialchars($entry['domain']); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4">
                                    <div class="empty-state">
                                        <i class="fas fa-ban"></i>
                                        <h4>No Blacklisted Domains</h4>
                                        <p>No domains have been blacklisted yet. Domains will be added here when detected as phishing during scans.</p>
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
    
    <script>
    function deleteDomain(domainId, domainName) {
        if (confirm('Are you sure you want to remove "' + domainName + '" from the blacklist?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/url_phishing_project/public/admin/blacklist';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const domainIdInput = document.createElement('input');
            domainIdInput.type = 'hidden';
            domainIdInput.name = 'domain_id';
            domainIdInput.value = domainId;
            
            form.appendChild(actionInput);
            form.appendChild(domainIdInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            }, 3000);
        });
    });
    </script>
</body>
</html> 