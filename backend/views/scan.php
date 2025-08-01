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
    <title>Scan URL - URL Phishing Detection</title>
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

        .scan-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .scan-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .scan-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .scan-form {
            background: var(--light-color);
            padding: 2.5rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .form-control {
            padding: 1rem 1.25rem;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            transition: var(--transition);
            font-size: 1rem;
            background: white;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
            outline: none;
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

        .loading {
            text-align: center;
            padding: 3rem;
            color: var(--primary-color);
        }

        .loading i {
            font-size: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Results Section */
        .result-container {
            margin-top: 2rem;
        }

        .result-header {
            background: var(--danger-color);
            color: white;
            padding: 2rem;
            border-radius: 10px 10px 0 0;
            text-align: center;
        }

        .result-header h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .result-header .risk-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            margin-top: 1rem;
            font-weight: 600;
        }

        .result-body {
            background: white;
            padding: 2rem;
            border-radius: 0 0 10px 10px;
        }

        .url-info {
            background: var(--light-color);
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            border-left: 5px solid var(--primary-color);
        }

        .url-display {
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 1.1rem;
            color: var(--dark-color);
            word-break: break-all;
            padding: 1rem;
            background: white;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }

        .risk-indicator {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .risk-level {
            background: var(--danger-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .confidence-score {
            background: var(--warning-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .info-card h4 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .info-card h4 i {
            color: var(--primary-color);
            font-size: 1.2rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid #f8f9fa;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 500;
            color: var(--secondary-color);
        }

        .info-value {
            font-weight: 600;
            color: var(--dark-color);
            max-width: 200px;
            text-align: right;
        }

        .info-value.long-text {
            max-width: 300px;
            font-size: 0.875rem;
            line-height: 1.4;
        }

        .info-value.very-long-text {
            max-width: 400px;
            font-size: 0.8rem;
            line-height: 1.3;
            word-break: break-word;
        }

        .text-tooltip {
            position: relative;
            cursor: pointer;
        }

        .text-tooltip:hover::after {
            content: attr(data-full-text);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: var(--dark-color);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            white-space: pre-wrap;
            max-width: 400px;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            margin-bottom: 0.5rem;
        }

        .text-tooltip:hover::before {
            content: '';
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            border: 5px solid transparent;
            border-top-color: var(--dark-color);
            margin-bottom: -5px;
        }

        .truncated-text {
            display: inline-block;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            vertical-align: bottom;
        }

        .expandable-text {
            position: relative;
            cursor: pointer;
            transition: var(--transition);
        }

        .expandable-text:hover {
            color: var(--primary-color);
        }

        .expandable-text.expanded .truncated-text {
            white-space: normal;
            max-width: none;
        }

        .expand-icon {
            margin-left: 0.5rem;
            font-size: 0.75rem;
            transition: var(--transition);
        }

        .expandable-text.expanded .expand-icon {
            transform: rotate(180deg);
        }

        .nameserver-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .nameserver-item {
            background: var(--light-color);
            padding: 0.75rem;
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.875rem;
        }

        .status-list {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .status-item {
            background: rgba(78, 115, 223, 0.1);
            padding: 0.5rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            color: var(--primary-color);
            border-left: 2px solid var(--primary-color);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-success {
            background: var(--success-color);
            color: white;
        }

        .status-danger {
            background: var(--danger-color);
            color: white;
        }

        .status-warning {
            background: var(--warning-color);
            color: white;
        }

        .status-info {
            background: var(--primary-color);
            color: white;
        }

        .pattern-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .pattern-item {
            padding: 1.5rem;
            border-radius: var(--border-radius);
            text-align: center;
            transition: var(--transition);
        }

        .pattern-item:hover {
            transform: scale(1.05);
        }

        .pattern-item.success {
            background: var(--success-color);
            color: white;
        }

        .pattern-item.danger {
            background: var(--danger-color);
            color: white;
        }

        .pattern-item.warning {
            background: var(--warning-color);
            color: white;
        }

        .pattern-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .pattern-label {
            font-size: 0.875rem;
            opacity: 0.9;
        }

        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .scan-info {
            background: var(--light-color);
            padding: 2rem;
            border-radius: var(--border-radius);
            margin-top: 2rem;
            border: 1px solid #e9ecef;
        }

        .scan-info h3 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        .scan-info ul {
            list-style: none;
            padding: 0;
        }

        .scan-info li {
            padding: 0.5rem 0;
            color: var(--secondary-color);
            position: relative;
            padding-left: 1.5rem;
        }

        .scan-info li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success-color);
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
            
            .pattern-grid {
                grid-template-columns: 1fr;
            }

            .info-value {
                max-width: 150px;
                font-size: 0.8rem;
            }

            .info-value.long-text {
                max-width: 200px;
            }

            .info-value.very-long-text {
                max-width: 250px;
            }

            .risk-indicator {
                flex-direction: column;
                gap: 0.5rem;
            }

            .result-header h3 {
                font-size: 2rem;
            }

            .url-display {
                font-size: 1rem;
                padding: 0.75rem;
            }
        }

        .result-badge {
            background: linear-gradient(135deg, var(--primary-color), #224abe);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }

        .result-badge.bg-danger {
            background: linear-gradient(135deg, #e74a3b, #c23d2e);
        }

        .result-badge.bg-success {
            background: linear-gradient(135deg, #1cc88a, #17a673);
        }

        .result-icon {
            margin-bottom: 1rem;
        }

        .result-content {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .result-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0 0 1rem 0;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .confidence-display {
            width: 100%;
            max-width: 300px;
        }

        .confidence-label {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .confidence-value {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .confidence-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            overflow: hidden;
        }

        .confidence-fill {
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 4px;
            transition: width 0.8s ease;
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
            <a href="/url_phishing_project/public/dashboard" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/scan" class="nav-link active">
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
            <h2>Scan URL</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'User'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="scan-container">
            <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="scan-section">
                <h2>Enter URL to Scan</h2>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form id="scanForm" action="/url_phishing_project/public/scan" method="POST">
                    <div class="scan-form">
                        <div class="mb-4">
                            <label for="url" class="form-label fw-bold">Enter URL to Scan</label>
                            <input type="url" class="form-control form-control-lg" id="url" name="url" 
                                   placeholder="https://example.com" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i> Scan URL
                        </button>
                    </div>
                </form>

                <div class="loading" style="display: none;">
                    <i class="fas fa-spinner"></i>
                    <p class="mt-3 fw-bold">Scanning URL...</p>
                    <p class="text-muted">This may take a few moments</p>
                </div>

                <?php if (isset($scanResult)): ?>
                        <?php
                    // Check if scan result exists
                    if (!isset($scanResult) || empty($scanResult)) {
                        echo '<div class="alert alert-danger">No scan result available.</div>';
                        return;
                    }

                    // Check for errors
                    if (isset($scanResult['error'])) {
                        echo '<div class="alert alert-danger">';
                        echo '<h4><i class="fas fa-exclamation-triangle"></i> Scan Error</h4>';
                        echo '<p>' . htmlspecialchars($scanResult['error']) . '</p>';
                        
                        if (isset($scanResult['status']) && $scanResult['status'] === 'not_found') {
                            echo '<div class="mt-3">';
                            echo '<h5>Domain Information:</h5>';
                            echo '<p><strong>URL:</strong> ' . htmlspecialchars($scanResult['url'] ?? 'N/A') . '</p>';
                            echo '<p><strong>Domain:</strong> ' . htmlspecialchars($scanResult['domain'] ?? 'N/A') . '</p>';
                            echo '<p><strong>Status:</strong> <span class="badge bg-warning">Domain Not Found</span></p>';
                            echo '</div>';
                        }
                        
                        echo '</div>';
                        return;
                    }

                    // Check for blacklisted domain
                    if (isset($scanResult['status']) && $scanResult['status'] === 'blacklisted') {
                        echo '<div class="alert alert-danger">';
                        echo '<h4><i class="fas fa-ban"></i> BLACKLISTED DOMAIN</h4>';
                        echo '<p><strong>This domain is blacklisted as a known phishing site!</strong></p>';
                        echo '<p><strong>Domain:</strong> ' . htmlspecialchars($scanResult['domain'] ?? 'N/A') . '</p>';
                        if (isset($scanResult['blacklist_info'])) {
                            echo '<p><strong>Added Date:</strong> ' . htmlspecialchars($scanResult['blacklist_info']['added_date'] ?? 'N/A') . '</p>';
                            echo '<p><strong>Reason:</strong> ' . htmlspecialchars($scanResult['blacklist_info']['reason'] ?? 'Known phishing domain') . '</p>';
                        }
                        echo '</div>';
                        
                        // Show WHOIS information even for blacklisted domains
                        if (isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])) {
                            echo '<div class="info-card">';
                            echo '<h4><i class="fas fa-info-circle"></i> WHOIS Information</h4>';
                            echo '<div class="whois-grid">';
                            foreach ($scanResult['whois_info'] as $key => $value) {
                                echo '<div class="whois-item">';
                                echo '<strong>' . htmlspecialchars($key) . ':</strong> ';
                                echo '<span>' . htmlspecialchars($value) . '</span>';
                                echo '</div>';
                            }
                            echo '</div>';
                            echo '</div>';
                        }
                        
                        return;
                    }

                    // Main scan result display
                    $isPhishing = isset($scanResult['is_phishing']) ? (bool)$scanResult['is_phishing'] : false;
                    $confidenceScore = $scanResult['confidence_score'] ?? 0;
                            $riskLevel = $isPhishing ? 'HIGH' : 'LOW';
                            $badgeClass = $isPhishing ? 'bg-danger' : 'bg-success';
                            $badgeText = $isPhishing ? 'PHISHING DETECTED' : 'SAFE URL';
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
                            <div class="confidence-display">
                                <div class="confidence-label">Confidence Rate</div>
                                <div class="confidence-value"><?php echo $confidenceScore; ?>%</div>
                                <div class="confidence-bar">
                                    <div class="confidence-fill" style="width: <?php echo $confidenceScore; ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                        <!-- URL Information -->
                        <div class="url-info">
                            <h4><i class="fas fa-link"></i> URL Information</h4>
                                    <div class="url-display"><?php echo htmlspecialchars($scanResult['url'] ?? ''); ?></div>
                                </div>

                    <!-- WHOIS Information -->
                    <?php if (isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])): ?>
                    <div class="info-card">
                        <h4><i class="fas fa-info-circle"></i> WHOIS Information</h4>
                        <div class="whois-grid">
                            <?php foreach ($scanResult['whois_info'] as $key => $value): ?>
                            <div class="whois-item">
                                <strong><?php echo htmlspecialchars($key); ?>:</strong> 
                                <span><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            <?php endforeach; ?>
                            </div>
                        </div>
                                <?php endif; ?>

                    <!-- Expert Analysis -->
                    <?php if (isset($scanResult['expert_analysis']) && !empty($scanResult['expert_analysis'])): ?>
                            <div class="info-card">
                        <h4><i class="fas fa-brain"></i> Expert Analysis</h4>
                        <div class="expert-analysis">
                            <?php echo nl2br(htmlspecialchars($scanResult['expert_analysis'])); ?>
                                </div>
                    </div>
                    <?php endif; ?>

                    <!-- URL Analysis -->
                                        <?php 
                    // Check if URL Analysis has any real data
                    $features = $scanResult['features'] ?? [];
                    $urlAnalysisKeys = ['URL Length', 'Domain Length', 'Path Length'];
                    $hasUrlAnalysis = false;
                    foreach ($urlAnalysisKeys as $key) {
                        if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== '') {
                            $hasUrlAnalysis = true;
                            break;
                        }
                    }
                    ?>
                    <?php if ($hasUrlAnalysis): ?>
                            <div class="info-card">
                                <h4><i class="fas fa-chart-bar"></i> URL Analysis</h4>
                        <div class="analysis-grid">
                            <?php foreach ($urlAnalysisKeys as $key): ?>
                            <?php if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== ''): ?>
                            <div class="analysis-item">
                                <strong><?php echo htmlspecialchars($key); ?>:</strong> 
                                <span><?php echo htmlspecialchars($features[$key]); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                                </div>
                                </div>
                    <?php endif; ?>

                            <!-- Security Checks -->
                    <?php
                    // Check if Security Checks has any real data
                    $securityKeys = ['Uses HTTPS', 'Contains IP Address', 'Contains Special Chars'];
                    $hasSecurity = false;
                    foreach ($securityKeys as $key) {
                        if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== '') {
                            $hasSecurity = true;
                            break;
                        }
                    }
                    ?>
                    <?php if ($hasSecurity): ?>
                            <div class="info-card">
                                <h4><i class="fas fa-lock"></i> Security Checks</h4>
                        <div class="analysis-grid">
                            <?php foreach ($securityKeys as $key): ?>
                            <?php if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== ''): ?>
                            <div class="analysis-item">
                                <strong><?php echo htmlspecialchars($key); ?>:</strong> 
                                <span class="<?php echo $features[$key] === true || $features[$key] === 'Yes' ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo htmlspecialchars($features[$key] === true ? 'Yes' : ($features[$key] === false ? 'No' : $features[$key])); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                                </div>
                                </div>
                    <?php endif; ?>

                        <!-- Suspicious Patterns -->
                    <?php
                    $suspiciousKeys = ['Contains Random String', 'Brand Name Count', 'Suspicious Words'];
                    $hasSuspicious = false;
                    foreach ($suspiciousKeys as $key) {
                        if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== '' && $features[$key] > 0) {
                            $hasSuspicious = true;
                            break;
                        }
                    }
                    ?>
                    <?php if ($hasSuspicious): ?>
                    <div class="info-card">
                            <h4><i class="fas fa-exclamation-triangle"></i> Suspicious Patterns</h4>
                        <div class="analysis-grid">
                            <?php foreach ($suspiciousKeys as $key): ?>
                            <?php if (isset($features[$key]) && $features[$key] !== 'N/A' && $features[$key] !== null && $features[$key] !== ''): ?>
                            <div class="analysis-item">
                                <strong><?php echo htmlspecialchars($key); ?>:</strong> 
                                <span class="<?php echo $features[$key] > 0 ? 'text-danger' : 'text-success'; ?>">
                                    <?php echo htmlspecialchars($features[$key]); ?>
                                </span>
                                </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                                </div>
                                </div>
                    <?php endif; ?>

                    <!-- Report as Phishing (for users) -->
                    <?php if ($isPhishing): ?>
                    <div class="info-card">
                        <h4><i class="fas fa-flag"></i> Report Phishing Site</h4>
                        <div class="blacklist-form">
                            <p><strong>Domain:</strong> <?php echo htmlspecialchars($scanResult['domain'] ?? ''); ?></p>
                            <p><strong>Reason:</strong> Reported by user as phishing</p>
                            <button type="button" class="btn btn-danger btn-lg" onclick="addToBlacklist('<?php echo htmlspecialchars($scanResult['domain'] ?? ''); ?>', 'Reported by user as phishing')">
                                <i class="fas fa-flag"></i> Report as Phishing
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Blacklisted Domain Notice -->
                    <?php if (isset($scanResult['already_blacklisted']) && $scanResult['already_blacklisted']): ?>
                    <div class="info-card alert alert-warning">
                        <h4><i class="fas fa-exclamation-triangle"></i> Domain Already Blacklisted</h4>
                        <div class="blacklist-notice">
                            <p><strong>Domain:</strong> <?php echo htmlspecialchars($scanResult['domain'] ?? ''); ?></p>
                            <p><strong>Status:</strong> This domain is already in our blacklist as a known phishing site.</p>
                            <p><strong>Added Date:</strong> <?php echo htmlspecialchars($scanResult['blacklist_info']['added_date'] ?? 'Unknown'); ?></p>
                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($scanResult['blacklist_info']['reason'] ?? 'Known phishing domain'); ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Scan Actions -->
                    <div class="scan-actions">
                        <a href="/url_phishing_project/public/scan" class="btn btn-primary">
                            <i class="fas fa-search"></i> Scan Another URL
                        </a>
                                    </div>
                                    </div>

                <style>
                .confidence-score {
                    margin-top: 15px;
                    text-align: center;
                }

                .confidence-bar {
                    width: 100%;
                    height: 20px;
                    background-color: #e9ecef;
                    border-radius: 10px;
                    overflow: hidden;
                    margin-top: 10px;
                }

                .confidence-fill {
                    height: 100%;
                    background: linear-gradient(90deg, #28a745, #ffc107, #dc3545);
                    transition: width 0.3s ease;
                }

                .expert-analysis {
                    background-color: #f8f9fa;
                    padding: 15px;
                    border-radius: 5px;
                    border-left: 4px solid #007bff;
                    white-space: pre-line;
                }

                .blacklist-form {
                    background-color: #fff3cd;
                    padding: 15px;
                    border-radius: 5px;
                    border: 1px solid #ffeaa7;
                }

                .blacklist-form .form-group {
                    margin-bottom: 15px;
                }

                .blacklist-form textarea {
                    resize: vertical;
                    min-height: 60px;
                }
                </style>

                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Handle blacklist form submission
                    const blacklistForm = document.querySelector('.blacklist-form');
                    if (blacklistForm) {
                        blacklistForm.addEventListener('submit', function(e) {
                            const reasonField = document.getElementById('blacklist_reason');
                            const customReason = reasonField.value.trim();
                            
                            if (customReason) {
                                // Update the hidden reason field with custom reason
                                const hiddenReason = this.querySelector('input[name="reason"]');
                                hiddenReason.value = customReason;
                            }
                        });
                    }
                });

                // Add domain to blacklist via AJAX
                function addToBlacklist(domain, reason) {
                    if (confirm('Are you sure you want to add ' + domain + ' to the blacklist?')) {
                        // Show loading state
                        const button = event.target;
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        button.disabled = true;

                        // Use AJAX to add to blacklist without page redirect
                        fetch('/url_phishing_project/public/blacklist', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: 'action=add&domain=' + encodeURIComponent(domain) + '&reason=' + encodeURIComponent(reason)
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Show success message
                                alert(data.message);
                                // Update the button to show it's been added
                                button.innerHTML = '<i class="fas fa-check"></i> Added to Blacklist';
                                button.className = 'btn btn-success btn-lg';
                                button.disabled = true;
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
                            alert('Failed to add domain to blacklist. Please try again.');
                            // Reset button
                            button.innerHTML = originalText;
                            button.disabled = false;
                        });
                    }
                }
                </script>
                                </div>
                            </div>
                        </div>
                <?php endif; ?>

                <div class="scan-info">
                    <h3><i class="fas fa-info-circle me-2"></i> About URL Scanning</h3>
                    <p class="mb-3">Our advanced URL scanning service checks for various phishing indicators including:</p>
                    <ul>
                        <li>Machine Learning based prediction with high accuracy</li>
                        <li>Domain reputation and age analysis</li>
                        <li>SSL certificate validity and security</li>
                        <li>URL structure and suspicious patterns</li>
                        <li>Real-time blacklist status checking</li>
                        <li>Comprehensive WHOIS information</li>
                        <li>Brand name and keyword analysis</li>
                    </ul>
                    <p class="mb-0"><strong>Note:</strong> The scan may take a few moments to complete. Please be patient while we analyze the URL thoroughly.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('scanForm')?.addEventListener('submit', function(e) {
            const loading = document.querySelector('.loading');
        const resultCard = document.querySelector('.scan-result');
            
            if (loading) loading.style.display = 'block';
            if (resultCard) resultCard.style.display = 'none';
            
            // Smooth scroll to loading
            loading?.scrollIntoView({ behavior: 'smooth' });
        });

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.info-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Function to toggle nameservers visibility
        function toggleNameservers(element) {
            const expandedList = element.nextElementSibling;
            const isExpanded = expandedList.style.display !== 'none';
            
            if (isExpanded) {
                expandedList.style.display = 'none';
                element.classList.remove('expanded');
                element.querySelector('.truncated-text').textContent = 'Show ' + expandedList.children.length + ' more nameservers';
                element.querySelector('.expand-icon').className = 'fas fa-chevron-down expand-icon';
            } else {
                expandedList.style.display = 'block';
                element.classList.add('expanded');
                element.querySelector('.truncated-text').textContent = 'Hide nameservers';
                element.querySelector('.expand-icon').className = 'fas fa-chevron-up expand-icon';
            }
        }

        // Add click handlers for expandable text
        document.addEventListener('DOMContentLoaded', function() {
            const expandableTexts = document.querySelectorAll('.expandable-text');
            expandableTexts.forEach(text => {
                text.addEventListener('click', function() {
                    this.classList.toggle('expanded');
                });
            });
        });
    </script>
</body>
</html> 