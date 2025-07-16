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
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .admin-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .add-domain-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            padding: 1rem;
            background: var(--light-color);
            border-radius: 10px;
        }

        .add-domain-form input,
        .add-domain-form select {
            flex: 1;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .add-domain-form select {
            flex: 0.5;
        }

        .add-domain-form button {
            padding: 0.5rem 1rem;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .add-domain-form button:hover {
            background: #224abe;
        }

        .domains-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .domains-table th,
        .domains-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .domains-table th {
            background: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .domains-table tr:hover {
            background: #f8f9fc;
        }

        .btn-delete {
            padding: 0.5rem 1rem;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            transition: background 0.3s ease;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .badge {
            padding: 0.5rem 0.75rem;
            border-radius: 5px;
            font-weight: 500;
        }

        .badge-reported { background: var(--warning-color); color: white; }
        .badge-confirmed { background: var(--danger-color); color: white; }
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
            <!-- <a href="/url_phishing_project/public/admin/reports" class="nav-link">
                <i class="fas fa-history"></i> Scan History
            </a> -->
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link active">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
                <i class="fas fa-file-alt"></i> Scan Reports
            </a>
            <a href="/url_phishing_project/public/import" class="nav-link">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <!-- <a href="/url_phishing_project/public/profile" class="nav-link">
                <i class="fas fa-user"></i> Profile
            </a> -->
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h2>Domain Blacklist</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <form class="add-domain-form" action="/url_phishing_project/public/admin/blacklist" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="text" name="domain" placeholder="Enter domain to blacklist" required>
                <select name="reason" required>
                    <option value="">Select reason...</option>
                    <option value="phishing">Phishing Attempt</option>
                    <option value="malware">Malware Distribution</option>
                    <option value="scam">Scam Website</option>
                    <option value="spam">Spam Source</option>
                    <option value="other">Other</option>
                </select>
                <button type="submit">Add Domain</button>
            </form>

            <table class="domains-table">
                <thead>
                    <tr>
                        <th>Domain</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Reports</th>
                        <th>Added By</th>
                        <th>Added Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($domains)): ?>
                        <?php foreach ($domains as $domain): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($domain['domain']); ?></td>
                            <td><?php echo htmlspecialchars($domain['reason']); ?></td>
                            <td>
                                <span class="badge <?php echo $domain['status'] === 'confirmed' ? 'badge-confirmed' : 'badge-reported'; ?>">
                                    <?php echo ucfirst($domain['status']); ?>
                                </span>
                            </td>
                            <td><?php echo $domain['report_count']; ?></td>
                            <td><?php echo htmlspecialchars($domain['added_by']); ?></td>
                            <td><?php echo date('Y-m-d H:i', strtotime($domain['created_at'])); ?></td>
                            <td>
                                <form action="/url_phishing_project/public/admin/blacklist" method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                                    <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to remove this domain from the blacklist?')">
                                        <i class="fas fa-trash"></i> Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">No blacklisted domains found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 