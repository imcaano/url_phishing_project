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
            max-width: 700px;
        }
        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        .details-table th {
            width: 180px;
        }
        .json-features-table {
            width: 100%;
            background: #f8f9fc;
            border-radius: 8px;
            padding: 1rem;
            font-size: 0.95rem;
        }
        .json-features-table th, .json-features-table td {
            padding: 0.5rem 1rem;
        }
        .back-link {
            margin-bottom: 1rem;
            display: inline-block;
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
            <a href="/url_phishing_project/public/admin/profile" class="nav-link">
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
            <h2>Scan Report Details</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>
        <div class="admin-section">
            <a href="/url_phishing_project/public/admin/reports" class="back-link"><i class="fas fa-arrow-left"></i> Back to Reports</a>
            <h2>Scan Report Details</h2>
            <table class="table details-table">
                <tr><th>ID</th><td><?php echo $report['id']; ?></td></tr>
                <tr><th>URL</th><td><?php echo htmlspecialchars($report['url']); ?></td></tr>
                <tr><th>Status</th><td><?php echo $report['is_phishing'] ? 'Phishing' : 'Safe'; ?></td></tr>
                <tr><th>Confidence</th><td><?php echo number_format($report['confidence_score'], 2); ?>%</td></tr>
                <tr><th>User</th><td><?php echo htmlspecialchars($report['username']); ?></td></tr>
                <tr><th>Scan Date</th><td><?php echo $report['scan_date']; ?></td></tr>
                <tr><th>Scan Features</th><td>
                    <?php
                    $features = json_decode($report['scan_features'], true);
                    if (is_array($features)) {
                        echo '<table class="json-features-table">';
                        foreach ($features as $key => $value) {
                            echo '<tr><th>' . htmlspecialchars($key) . '</th><td>' . htmlspecialchars($value) . '</td></tr>';
                        }
                        echo '</table>';
                    } else {
                        echo '<pre>' . htmlspecialchars($report['scan_features']) . '</pre>';
                    }
                    ?>
                </td></tr>
            </table>
        </div>
    </div>
</body>
</html> 