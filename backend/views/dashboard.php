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
    <title>Dashboard - URL Phishing Detection</title>
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

        .dashboard-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .dashboard-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--light-color);
            padding: 1.5rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            margin: 0;
            color: var(--dark-color);
            font-size: 1.5rem;
        }

        .stat-card p {
            margin: 0.5rem 0 0;
            color: var(--secondary-color);
            font-size: 1.1rem;
        }

        .recent-scans {
            margin-top: 2rem;
        }

        .scan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .scan-table th,
        .scan-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        .scan-table th {
            background: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .scan-table tr:hover {
            background: var(--light-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-badge.safe {
            background: var(--success-color);
            color: white;
        }

        .status-badge.phishing {
            background: var(--danger-color);
            color: white;
        }

        .status-badge.suspicious {
            background: var(--warning-color);
            color: white;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #224abe;
        }

        .scan-form {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .alert {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
        <!-- Sidebar -->
        <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt me-2"></i>Dashboard</h1>
            </div>
            <nav>
            <a href="/url_phishing_project/public/dashboard" class="nav-link active">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            <a href="/url_phishing_project/public/scan" class="nav-link">
                    <i class="fas fa-search"></i> Scan URL
                </a>
            <a href="/url_phishing_project/public/report" class="nav-link">
                <i class="fas fa-file-alt"></i> Scan Reports
                </a>
            <a href="/url_phishing_project/public/profile" class="nav-link">
                <i class="fas fa-user"></i> Profile
                </a>
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
        <div class="top-navbar">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h2>
            <div class="user-info">
                <span>User Dashboard</span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>

        <div class="dashboard-section">
            <h2>Quick Scan</h2>
            <form action="/url_phishing_project/public/scan" method="post" class="scan-form">
                <div class="form-group">
                    <label for="url">Enter URL to scan</label>
                    <input type="url" class="form-control" id="url" name="url" placeholder="https://example.com" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Scan URL
                </button>
            </form>
            </div>

        <div class="dashboard-section">
            <h2>Your Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-chart-line"></i>
                    <h3><?php echo $stats['total_scans']; ?></h3>
                    <p>Total Scans</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3><?php echo $stats['phishing_detected']; ?></h3>
                    <p>Phishing Detected</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-globe"></i>
                    <h3><?php echo htmlspecialchars($country); ?></h3>
                    <p>Your Country</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-shield-alt"></i>
                    <h3><?php echo $stats['safe_urls']; ?></h3>
                    <p>Safe URLs</p>
                </div>
            </div>
        </div>

        <div class="dashboard-section">
            <h2>Recent Scans</h2>
            <div class="recent-scans">
                <table class="scan-table">
                    <thead>
                        <tr>
                            <th>URL</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentScans as $scan): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($scan['url']); ?></td>
                            <td>
                                <span class="status-badge <?php echo $scan['is_phishing'] ? 'phishing' : 'safe'; ?>">
                                    <?php echo $scan['is_phishing'] ? 'Phishing' : 'Safe'; ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y H:i', strtotime($scan['scan_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 