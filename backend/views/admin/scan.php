<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Scan URL - URL Phishing Detection</title>
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

        .form-control {
            border-radius: 5px;
            padding: 10px 15px;
        }

        .btn-primary {
            padding: 10px 20px;
            border-radius: 5px;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading i {
            font-size: 2rem;
            color: var(--primary-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .feature-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .feature-item:last-child {
            border-bottom: none;
        }

        .feature-label {
            font-weight: 500;
            color: #666;
        }

        .feature-value {
            color: #333;
        }

        .risk-high {
            color: var(--danger-color);
        }

        .risk-medium {
            color: var(--warning-color);
        }

        .risk-low {
            color: var(--success-color);
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
            <a href="/url_phishing_project/public/admin/scan" class="nav-link active">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
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
            <!-- <a href="/url_phishing_project/public/admin/profile" class="nav-link">
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
            <h2>Scan URL</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <h2>URL Scanner</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form id="scanForm" action="/url_phishing_project/public/admin/scan" method="POST">
                <div class="mb-3">
                    <label for="url" class="form-label">Enter URL to Scan</label>
                    <input type="url" class="form-control" id="url" name="url" 
                           placeholder="https://example.com" required>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Scan URL
                </button>
            </form>

            <div class="loading mt-4">
                <i class="fas fa-spinner"></i>
                <p class="mt-2">Scanning URL...</p>
            </div>

            <?php if (isset($scanResult)): ?>
            <div class="result-card mt-4">
                <div class="card <?php echo $scanResult['is_phishing'] ? 'border-danger' : 'border-success'; ?>">
                    <div class="card-header <?php echo $scanResult['is_phishing'] ? 'bg-danger text-white' : 'bg-success text-white'; ?>">
                        <h3 class="mb-0">Scan Results</h3>
                    </div>
                    <div class="card-body">
                        <!-- URL Info -->
                        <div class="mb-4">
                            <h4><i class="fas fa-link"></i> URL Information</h4>
                            <p class="lead"><?php echo htmlspecialchars($scanResult['url']); ?></p>
                            <div class="alert alert-<?php echo $scanResult['is_phishing'] ? 'danger' : 'success'; ?>">
                                <strong>Risk Level:</strong> <?php echo $scanResult['risk_level']; ?>
                                <br>
                                <strong>Confidence Score:</strong> <?php echo number_format($scanResult['confidence_score'], 2); ?>%
                            </div>
                        </div>

                        <!-- WHOIS Information -->
                        <?php if (isset($scanResult['whois'])): ?>
                        <div class="mb-4">
                            <h4><i class="fas fa-info-circle"></i> WHOIS Information</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Domain Age
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Domain Age']; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Domain Status
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Domain Status']; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Registrar
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Domain Registrar']; ?></span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-group">
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Expiration Date
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Domain Expiry']; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Last Updated
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Last Updated']; ?></span>
                                        </li>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            Nameservers
                                            <span class="badge bg-primary rounded-pill"><?php echo $scanResult['whois']['Nameservers']; ?></span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Features Analysis -->
                        <div class="row">
                            <!-- URL Analysis -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-search"></i> URL Analysis</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                URL Length
                                                <span class="badge bg-primary rounded-pill"><?php echo $scanResult['features']['URL Length']; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Domain Length
                                                <span class="badge bg-primary rounded-pill"><?php echo $scanResult['features']['Domain Length']; ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Path Length
                                                <span class="badge bg-primary rounded-pill"><?php echo $scanResult['features']['Path Length']; ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Checks -->
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-lock"></i> Security Checks</h5>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                HTTPS
                                                <span class="badge <?php echo $scanResult['features']['Uses HTTPS'] === 'Yes' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $scanResult['features']['Uses HTTPS']; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                IP Address
                                                <span class="badge <?php echo $scanResult['features']['Contains IP Address'] === 'No' ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo $scanResult['features']['Contains IP Address']; ?>
                                                </span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Special Characters
                                                <span class="badge <?php echo $scanResult['features']['Contains Special Chars'] === 'No' ? 'bg-success' : 'bg-warning'; ?>">
                                                    <?php echo $scanResult['features']['Contains Special Chars']; ?>
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Suspicious Patterns -->
                            <div class="col-md-12 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Suspicious Patterns</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="alert <?php echo $scanResult['features']['Contains Random String'] === 'No' ? 'alert-success' : 'alert-danger'; ?>">
                                                    <strong>Random Strings:</strong> <?php echo $scanResult['features']['Contains Random String']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="alert <?php echo $scanResult['features']['Contains Brand Names'] === 0 ? 'alert-success' : 'alert-warning'; ?>">
                                                    <strong>Brand Names:</strong> <?php echo $scanResult['features']['Contains Brand Names']; ?>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="alert <?php echo $scanResult['features']['Suspicious Words'] === 0 ? 'alert-success' : 'alert-danger'; ?>">
                                                    <strong>Suspicious Words:</strong> <?php echo $scanResult['features']['Suspicious Words']; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('scanForm').addEventListener('submit', function(e) {
            document.querySelector('.loading').style.display = 'block';
            document.querySelector('.result-card').style.display = 'none';
        });
    </script>
</body>
</html> 