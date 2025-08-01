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

        .welcome-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .welcome-section h2 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .stat-card h3 {
            margin: 0;
            color: var(--dark-color);
        }

        .stat-card p {
            margin: 0.5rem 0 0;
            color: var(--secondary-color);
        }

        .stat-card.total-scans i { color: var(--primary-color); }
        .stat-card.phishing-detected i { color: var(--danger-color); }
        .stat-card.safe-urls i { color: var(--success-color); }
        .stat-card.accuracy-rate i { color: var(--warning-color); }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt me-2"></i>URL Scanner</h1>
        </div>
        <nav>
            <a href="/url_phishing_project/public/dashboard" class="nav-link active">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/scan" class="nav-link">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/history" class="nav-link">
                <i class="fas fa-history"></i> Scan History
            </a>
            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
            <a href="/url_phishing_project/public/import" class="nav-link">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <?php endif; ?>
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
            <h2>Dashboard</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user']['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="welcome-section">
            <h2>Welcome to URL Phishing Detection System</h2>
            <p>Monitor your URL scanning activities and stay protected from phishing threats.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card total-scans">
                <i class="fas fa-chart-line"></i>
                <h3>Total Scans</h3>
                <p><?php echo $totalScans; ?></p>
            </div>
            <div class="stat-card phishing-detected">
                <i class="fas fa-exclamation-triangle"></i>
                <h3>Phishing Detected</h3>
                <p><?php echo $phishingScans; ?></p>
            </div>
            <div class="stat-card safe-urls">
                <i class="fas fa-check-circle"></i>
                <h3>Safe URLs</h3>
                <p><?php echo $safeScans; ?></p>
            </div>
            <div class="stat-card accuracy-rate">
                <i class="fas fa-bullseye"></i>
                <h3>Accuracy Rate</h3>
                <p><?php echo $accuracyRate; ?>%</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 