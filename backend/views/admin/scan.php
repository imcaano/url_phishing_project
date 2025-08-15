<?php
// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /url_phishing_project/public/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin URL Scanner - URL Phishing Detection</title>
    <link rel="icon" type="image/png" href="/url_phishing_project/public/assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

        * {
            box-sizing: border-box;
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
            border-radius: 15px;
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
        }

        .scan-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .scan-section h2 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            text-align: center;
        }





        .scan-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .form-label {
            color: var(--dark-color);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #e3e6f0;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .btn {
            border-radius: 10px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            box-shadow: 0 4px 15px rgba(78, 115, 223, 0.3);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(78, 115, 223, 0.4);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #6c757d 100%);
            box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
            color: white;
        }

        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
            box-shadow: 0 4px 15px rgba(246, 194, 62, 0.3);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(246, 194, 62, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #13855c 100%);
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(28, 200, 138, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            box-shadow: 0 4px 15px rgba(231, 74, 59, 0.3);
            color: white;
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(231, 74, 59, 0.4);
        }

        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--secondary-color);
        }

        .loading i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .scan-result {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }

        .result-badge {
            display: flex;
            align-items: center;
            gap: 2rem;
            padding: 2rem;
            border-radius: 15px;
            color: white;
            margin-bottom: 2rem;
            text-align: center;
        }

        .result-badge.bg-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
        }

        .result-badge.bg-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #13855c 100%);
        }

        .result-icon {
            flex-shrink: 0;
        }

        .result-content h1 {
            margin: 0;
            font-size: 2.5rem;
            font-weight: 700;
        }

        .confidence-score, .risk-level {
            margin: 0.5rem 0;
            font-size: 1.2rem;
            opacity: 0.9;
        }

        .url-info, .info-card {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1.5rem 0;
        }

        .url-info h4, .info-card h4 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .url-display {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 2px solid #e3e6f0;
            font-family: monospace;
            word-break: break-all;
            margin-bottom: 1rem;
        }

        .domain-info {
            margin: 0;
            font-weight: 600;
        }

        .whois-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .whois-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .whois-item strong {
            color: var(--dark-color);
        }

        .analysis-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .analysis-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .auto-blacklist-notification {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.2);
        }

        .notification-icon {
            flex-shrink: 0;
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-content {
            flex: 1;
        }

        .notification-content h4 {
            margin: 0 0 0.5rem 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .notification-content p {
            margin: 0 0 1rem 0;
            opacity: 0.9;
        }

        .notification-details {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .scan-actions {
            margin-top: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .list-group-item {
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }

        .text-danger {
            color: var(--danger-color) !important;
        }

        .text-success {
            color: var(--success-color) !important;
        }

        .text-warning {
            color: var(--warning-color) !important;
        }

        .text-info {
            color: var(--info-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .status-indicator {
            text-align: center;
            padding: 1rem;
            border-radius: 10px;
            margin: 1rem 0;
            font-weight: 600;
        }

        .status-safe {
            background: rgba(28, 200, 138, 0.1);
            color: var(--success-color);
            border: 2px solid var(--success-color);
        }

        .status-phishing {
            background: rgba(231, 74, 59, 0.1);
            color: var(--danger-color);
            border: 2px solid var(--danger-color);
        }

        .scan-summary {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: center;
            border: 2px solid #dee2e6;
        }

        .scan-summary h3 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .scan-summary p {
            color: var(--secondary-color);
            margin-bottom: 0;
        }



        .whois-sections {
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }

        .whois-section {
            background: #f8f9fc;
            border-radius: 10px;
            padding: 1.5rem;
            border: 1px solid #e3e6f0;
        }

        .whois-section h5 {
            color: var(--dark-color);
            margin-bottom: 1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .whois-section h5 i {
            font-size: 1.2rem;
        }

        .whois-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .whois-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            border: 1px solid #e3e6f0;
        }

        .whois-item strong {
            color: var(--dark-color);
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .whois-item span {
            color: var(--secondary-color);
            font-size: 0.95rem;
            word-break: break-word;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt"></i> Admin Panel</h1>
        </div>
        <nav>
            <a href="/url_phishing_project/public/admin/dashboard" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/admin/scan" class="nav-link active">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
                <i class="fas fa-chart-bar"></i> Reports
            </a>
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Blacklist
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
            <h2>Admin URL Scanner</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        

            <div class="scan-section">
                <h2>Enter URL to Scan</h2>


            <?php if (isset($viewError)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $viewError; ?>
                    </div>
                <?php endif; ?>

            <form id="scanForm" action="/url_phishing_project/public/admin/scan" method="POST">
                    <div class="scan-form">
                        <div class="mb-4">
                            <label for="url" class="form-label fw-bold">Enter URL to Scan</label>
                            <input type="url" class="form-control form-control-lg" id="url" name="url" 
                           placeholder="https://example.com" required>
                </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i> Scan URL
                </button>
                    </div>
                    </div>
                </form>

                <div class="loading" style="display: none;">
                <i class="fas fa-spinner fa-spin"></i>
                    <p class="mt-3 fw-bold">Scanning URL...</p>
                    <p class="text-muted">This may take a few moments</p>
                            </div>

            <?php if (isset($viewScanResult)): ?>
                <?php if (isset($viewScanResult['error'])): ?>
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-robot"></i> ML Model Error</h4>
                        <p><?php echo htmlspecialchars($viewScanResult['error']); ?></p>
                    </div>
                <?php elseif (isset($viewScanResult['status']) && $viewScanResult['status'] === 'error'): ?>
                    <div class="alert alert-danger">
                        <h4><i class="fas fa-robot"></i> ML Model Error</h4>
                        <p><?php echo htmlspecialchars($viewScanResult['error'] ?? 'Unknown error occurred'); ?></p>
                    </div>
                <?php elseif (isset($viewScanResult['prediction'])): ?>
                    <?php
                    $isPhishing = ($viewScanResult['prediction'] === 'phishing');
                    $badgeClass = $isPhishing ? 'bg-danger' : 'bg-success';
                    $badgeText = $isPhishing ? 'PHISHING DETECTED' : 'SAFE';
                    $confidenceScore = $viewScanResult['confidence_score'] ?? 0;
                    $riskLevel = $viewScanResult['risk_level'] ?? 'N/A';
                    $domain = parse_url($viewScanResult['url'], PHP_URL_HOST);
                        ?>

                    <div class="scan-result">
                        <!-- Main Result Badge -->
                        <div class="result-badge <?php echo $badgeClass; ?>">
                            <div class="result-icon">
                                <?php if ($isPhishing): ?>
                                    <i class="fas fa-exclamation-triangle fa-3x"></i>
                                <?php else: ?>
                                    <i class="fas fa-shield-check fa-3x"></i>
                                <?php endif; ?>
                            </div>
                            <div class="result-content">
                                <h1 class="result-title"><?php echo $badgeText; ?></h1>
                                <p class="confidence-score">ML Confidence: <?php echo $confidenceScore; ?>%</p>
                                <p class="risk-level">Risk Level: <?php echo $riskLevel; ?></p>
                            </div>
                        </div>

                        <!-- Status Summary -->
                        <div class="scan-summary">
                            <h3><i class="fas fa-info-circle me-2"></i>Scan Summary</h3>
                            <?php if ($isPhishing): ?>
                                <p><strong>⚠️ Phishing Domain Detected:</strong> This domain has been automatically added to the blacklist due to high confidence phishing detection.</p>
                                <p><strong>🕒 Auto-blacklisted:</strong> <?php echo date('M d, Y H:i:s'); ?></p>
                                <p><strong>🔧 Action Required:</strong> Use "Back to Safe" button if this is a false positive</p>
                            <?php else: ?>
                                <p><strong>✅ Safe Domain Confirmed:</strong> This domain has been verified as safe by our ML model.</p>
                                <p><strong>🔒 Security Status:</strong> No threats detected</p>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Auto-Blacklist Notification (only for phishing) -->
                        <?php if ($isPhishing && isset($viewScanResult['auto_blacklisted']) && $viewScanResult['auto_blacklisted']): ?>
                        <div class="auto-blacklist-notification">
                            <div class="notification-icon">
                                <i class="fas fa-ban fa-2x"></i>
                            </div>
                            <div class="notification-content">
                                <h4><i class="fas fa-shield-alt"></i> Domain Auto-Blacklisted</h4>
                                <p><?php echo htmlspecialchars($viewScanResult['blacklist_message'] ?? 'Domain automatically added to blacklist.'); ?></p>
                                <div class="notification-details">
                                    <span><i class="fas fa-clock"></i> <?php echo date('M d, Y H:i:s'); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                                <!-- URL Information -->
                                <div class="url-info">
                                    <h4><i class="fas fa-link"></i> URL Information</h4>
                            <div class="url-display"><?php echo htmlspecialchars($viewScanResult['url'] ?? ''); ?></div>
                            <p class="domain-info"><strong>Domain:</strong> <?php echo htmlspecialchars($domain); ?></p>
                        </div>

                        <!-- WHOIS Information -->
                        <?php if (isset($viewScanResult['whois_info']) && !empty($viewScanResult['whois_info'])): ?>
                        <div class="info-card">
                            <h4><i class="fas fa-database"></i> Complete WHOIS Information</h4>
                            <div class="whois-sections">
                                <!-- Domain Registration Info -->
                                <div class="whois-section">
                                    <h5><i class="fas fa-globe text-primary"></i> Domain Registration</h5>
                            <div class="whois-grid">
                                        <?php 
                                        $registrationFields = ['domain', 'registrar', 'creation_date', 'expiry_date', 'updated_date', 'status'];
                                        foreach ($registrationFields as $field): 
                                            if (isset($viewScanResult['whois_info'][$field]) && $viewScanResult['whois_info'][$field] !== 'Unknown' && $viewScanResult['whois_info'][$field] !== null && $viewScanResult['whois_info'][$field] !== ''):
                                        ?>
                                <div class="whois-item">
                                            <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</strong> 
                                            <span><?php echo htmlspecialchars($viewScanResult['whois_info'][$field]); ?></span>
                                </div>
                                        <?php endif; endforeach; ?>
                            </div>
                        </div>

                                <!-- Owner Information -->
                                <div class="whois-section">
                                    <h5><i class="fas fa-user text-success"></i> Owner Information</h5>
                                    <div class="whois-grid">
                                        <?php 
                                        $ownerFields = ['registrant_name', 'registrant_organization', 'registrant_email', 'registrant_phone', 'registrant_address'];
                                        foreach ($ownerFields as $field): 
                                            if (isset($viewScanResult['whois_info'][$field]) && $viewScanResult['whois_info'][$field] !== 'Unknown' && $viewScanResult['whois_info'][$field] !== null && $viewScanResult['whois_info'][$field] !== ''):
                                        ?>
                                        <div class="whois-item">
                                            <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</strong> 
                                            <span><?php echo htmlspecialchars($viewScanResult['whois_info'][$field]); ?></span>
                                        </div>
                                        <?php endif; endforeach; ?>
                                    </div>
                                </div>

                                <!-- Technical Information -->
                                <div class="whois-section">
                                    <h5><i class="fas fa-cogs text-info"></i> Technical Information</h5>
                                    <div class="whois-grid">
                        <?php
                                        $technicalFields = ['nameservers', 'whois_server', 'dnssec', 'tech_email', 'admin_email'];
                                        foreach ($technicalFields as $field): 
                                            if (isset($viewScanResult['whois_info'][$field]) && $viewScanResult['whois_info'][$field] !== 'Unknown' && $viewScanResult['whois_info'][$field] !== null && $viewScanResult['whois_info'][$field] !== ''):
                                        ?>
                                        <div class="whois-item">
                                            <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</strong> 
                                            <span><?php echo htmlspecialchars($viewScanResult['whois_info'][$field]); ?></span>
                                        </div>
                                        <?php endif; endforeach; ?>
                            </div>
                                    </div>

                                <!-- Additional Information -->
                                <div class="whois-section">
                                    <h5><i class="fas fa-plus-circle text-warning"></i> Additional Details</h5>
                                    <div class="whois-grid">
                                        <?php foreach ($viewScanResult['whois_info'] as $key => $value): ?>
                        <?php
                                            $excludedFields = array_merge($registrationFields, $ownerFields, $technicalFields);
                                            if (!in_array($key, $excludedFields) && $value !== 'Unknown' && $value !== null && $value !== ''): 
                                        ?>
                                        <div class="whois-item">
                                            <strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $key))); ?>:</strong> 
                                            <span><?php echo htmlspecialchars($value); ?></span>
                                </div>
                                <?php endif; ?>
                                <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                                    </div>
                        <?php endif; ?>

                        <!-- ML Model Analysis -->
                        <?php if (isset($viewScanResult['expert_analysis'])): ?>
                    <div class="info-card">
                            <h4><i class="fas fa-robot"></i> ML Model Analysis</h4>
                            <div class="alert <?php echo $isPhishing ? 'alert-danger' : 'alert-success'; ?>">
                                <strong><?php echo htmlspecialchars($viewScanResult['expert_analysis']['summary'] ?? ''); ?></strong>
                            </div>
                            
                            <?php if ($isPhishing && isset($viewScanResult['expert_analysis']['risk_factors']) && !empty($viewScanResult['expert_analysis']['risk_factors'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-exclamation-triangle text-warning me-2"></i>Why This URL is Phishing:</h5>
                                <ul class="list-group">
                                    <?php foreach ($viewScanResult['expert_analysis']['risk_factors'] as $factor): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-times text-danger me-2"></i><?php echo htmlspecialchars($factor); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($viewScanResult['expert_analysis']['security_features']) && !empty($viewScanResult['expert_analysis']['security_features'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-shield-alt text-success me-2"></i>Security Features:</h5>
                                <ul class="list-group">
                                    <?php foreach ($viewScanResult['expert_analysis']['security_features'] as $feature): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($feature); ?>
                                    </li>
                            <?php endforeach; ?>
                                </ul>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (isset($viewScanResult['expert_analysis']['recommendations']) && !empty($viewScanResult['expert_analysis']['recommendations'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-lightbulb text-info me-2"></i>Recommendations:</h5>
                                <ul class="list-group">
                                    <?php foreach ($viewScanResult['expert_analysis']['recommendations'] as $rec): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-arrow-right text-primary me-2"></i><?php echo htmlspecialchars($rec); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>

                                                <!-- Action Buttons (only show after scan results) -->
                        <div class="scan-actions">
                            <?php if ($isPhishing): ?>
                                <button class="btn btn-warning btn-lg" onclick="backToSafe('<?php echo htmlspecialchars($domain); ?>')">
                                    <i class="fas fa-shield-check me-2"></i> Back to Safe
                                </button>
                            <?php else: ?>
                                <button class="btn btn-success btn-lg" onclick="removeFromBlacklist('<?php echo htmlspecialchars($viewScanResult['url']); ?>')">
                                    <i class="fas fa-undo me-2"></i>Remove from Blacklist (if listed)
                                </button>
                                <button class="btn btn-warning btn-lg" onclick="restartScan()">
                                    <i class="fas fa-redo me-2"></i> Restart Scan
                                </button>
                            <?php endif; ?>
                            
                            <a href="/url_phishing_project/public/admin/dashboard" class="btn btn-secondary btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i> Back to Dashboard
                            </a>
                        </div>
                        </div>
                <?php endif; ?>
                    <?php endif; ?>

                        <!-- Blacklisted Domain Notice -->
            <?php if (isset($viewScanResult['already_blacklisted']) && $viewScanResult['already_blacklisted']): ?>
                        <div class="info-card alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle"></i> Domain Already Blacklisted</h4>
                            <div class="blacklist-notice">
                    <p><strong>Domain:</strong> <?php echo htmlspecialchars($viewScanResult['domain'] ?? ''); ?></p>
                                <p><strong>Status:</strong> This domain is already in our blacklist as a known phishing site.</p>
                    <p><strong>Added Date:</strong> <?php echo htmlspecialchars($viewScanResult['blacklist_info']['added_date'] ?? 'Unknown'); ?></p>
                    <p><strong>Reason:</strong> <?php echo htmlspecialchars($viewScanResult['blacklist_info']['reason'] ?? 'Known phishing domain'); ?></p>
                            </div>
                        </div>
                        <?php endif; ?>

            <!-- Bottom Navigation (only show when no scan results) -->
            <?php if (!isset($viewScanResult) || !isset($viewScanResult['prediction'])): ?>
                        <div class="scan-actions">
                <a href="/url_phishing_project/public/admin/dashboard" class="btn btn-secondary btn-lg">
                    <i class="fas fa-tachometer-alt me-2"></i> Back to Dashboard
                </a>
            </div>
            <?php endif; ?>
                        </div>
                    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show loading when form is submitted
        document.getElementById('scanForm').addEventListener('submit', function() {
            document.querySelector('.loading').style.display = 'block';
        });

        // Function to restart scan
        function restartScan() {
            if (confirm('Are you sure you want to restart the scan? This will clear all current results.')) {
                // Clear the form
                document.getElementById('scanForm').reset();
                
                // Hide any scan results
                const scanResult = document.querySelector('.scan-result');
                if (scanResult) {
                    scanResult.style.display = 'none';
                }
                
                // Show the form prominently
                document.querySelector('.scan-form').style.display = 'block';
                
                // Focus on the URL input
                document.getElementById('url').focus();
                
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-info alert-dismissible fade show';
                alertDiv.innerHTML = `
                    <i class="fas fa-info-circle me-2"></i>
                    Scan restarted! Enter a new URL to scan.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.querySelector('.scan-section').insertBefore(alertDiv, document.querySelector('.scan-form'));
            }
        }



        // Function to remove domain from blacklist
        function removeFromBlacklist(url) {
            if (confirm('Are you sure you want to remove this domain from the blacklist?')) {
                fetch('/url_phishing_project/public/admin/scan', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=remove&url=' + encodeURIComponent(url)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Domain removed from blacklist successfully!');
                        location.reload();
                    } else {
                        alert('Failed to remove domain from blacklist: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while removing domain from blacklist.');
                });
            }
        }

        // Function to mark phishing domain as safe and remove from blacklist
        function backToSafe(domain) {
            if (confirm('Are you sure you want to mark this domain as safe and remove it from the blacklist?')) {
                            fetch('/url_phishing_project/public/admin/blacklist', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                    body: 'action=restore&domain=' + encodeURIComponent(domain)
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                        alert('Domain marked as safe and removed from blacklist successfully!');
                        location.reload();
                                } else {
                        alert('Failed to mark domain as safe: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                    alert('An error occurred while marking domain as safe.');
                            });
                        }
                    }
    </script>
</body>
</html> 
