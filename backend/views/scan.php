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
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .form-control {
            padding: 1rem 1.25rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
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

        .confidence-score {
            margin-top: 15px;
            text-align: center;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .risk-level {
            margin-top: 10px;
            text-align: center;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .url-info {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 8px;
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

        .domain-info {
            margin-top: 1rem;
            font-size: 1.1rem;
        }

        .info-card {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
            margin-bottom: 2rem;
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

        .whois-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .whois-item {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid var(--primary-color);
        }

        .whois-item strong {
            color: var(--dark-color);
        }

        .list-group {
            margin-top: 1rem;
        }

        .list-group-item {
            border: none;
            background: var(--light-color);
            margin-bottom: 0.5rem;
            border-radius: 8px;
            padding: 1rem;
        }

        .scan-actions {
            text-align: center;
            margin-top: 2rem;
        }

        .scan-info {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 8px;
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
            
            .whois-grid {
                grid-template-columns: 1fr;
            }
            
            .result-title {
                font-size: 2rem;
            }

            .url-display {
                font-size: 1rem;
                padding: 0.75rem;
            }
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
                <?php if (isset($viewError)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo $viewError; ?>
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
                    } else {
                    // Check for errors
                    if (isset($scanResult['error'])) {
                        echo '<div class="alert alert-danger">';
                            echo '<h4><i class="fas fa-robot"></i> ML Model Error</h4>';
                        echo '<p>' . htmlspecialchars($scanResult['error']) . '</p>';
                            echo '</div>';
                        } elseif (isset($scanResult['status']) && $scanResult['status'] === 'error') {
                        echo '<div class="alert alert-danger">';
                            echo '<h4><i class="fas fa-robot"></i> ML Model Error</h4>';
                            echo '<p>' . htmlspecialchars($scanResult['error'] ?? 'Unknown error occurred') . '</p>';
                        echo '</div>';
                        } elseif (isset($scanResult['prediction'])) {
                            // Display ML Model Results
                            $isPhishing = ($scanResult['prediction'] === 'phishing');
                    $badgeClass = $isPhishing ? 'bg-danger' : 'bg-success';
                            $badgeText = $isPhishing ? 'PHISHING DETECTED' : 'SAFE';
                            $confidenceScore = $scanResult['confidence_score'] ?? 0;
                            $riskLevel = $scanResult['risk_level'] ?? 'N/A';
                            $domain = parse_url($scanResult['url'], PHP_URL_HOST);
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
                    
                        <!-- URL Information -->
                        <div class="url-info">
                            <h4><i class="fas fa-link"></i> URL Information</h4>
                                    <div class="url-display"><?php echo htmlspecialchars($scanResult['url'] ?? ''); ?></div>
                            <p class="domain-info"><strong>Domain:</strong> <?php echo htmlspecialchars($domain); ?></p>
                                </div>

                    <!-- WHOIS Information -->
                    <?php if (isset($scanResult['whois_info']) && !empty($scanResult['whois_info'])): ?>
                    <div class="info-card">
                        <h4><i class="fas fa-info-circle"></i> WHOIS Information</h4>
                        <div class="whois-grid">
                            <?php foreach ($scanResult['whois_info'] as $key => $value): ?>
                            <?php if ($value !== 'Unknown' && $value !== null && $value !== ''): ?>
                            <div class="whois-item">
                                <strong><?php echo htmlspecialchars($key); ?>:</strong> 
                                <span><?php echo htmlspecialchars($value); ?></span>
                                </div>
                            <?php endif; ?>
                            <?php endforeach; ?>
                            </div>
                        </div>
                                <?php endif; ?>

                        <!-- ML Model Analysis -->
                        <?php if (isset($scanResult['expert_analysis'])): ?>
                            <div class="info-card">
                            <h4><i class="fas fa-robot"></i> ML Model Analysis</h4>
                            <div class="alert <?php echo $isPhishing ? 'alert-danger' : 'alert-success'; ?>">
                                <strong><?php echo htmlspecialchars($scanResult['expert_analysis']['summary'] ?? ''); ?></strong>
                                </div>
                            
                            <?php if ($isPhishing && isset($scanResult['expert_analysis']['risk_factors']) && !empty($scanResult['expert_analysis']['risk_factors'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-exclamation-triangle text-warning me-2"></i>Why This URL is Phishing:</h5>
                                <ul class="list-group">
                                    <?php foreach ($scanResult['expert_analysis']['risk_factors'] as $factor): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-times text-danger me-2"></i><?php echo htmlspecialchars($factor); ?>
                                    </li>
                            <?php endforeach; ?>
                                </ul>
                                </div>
                    <?php endif; ?>

                            <?php if (isset($scanResult['expert_analysis']['security_features']) && !empty($scanResult['expert_analysis']['security_features'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-shield-alt text-success me-2"></i>Security Features:</h5>
                                <ul class="list-group">
                                    <?php foreach ($scanResult['expert_analysis']['security_features'] as $feature): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-check text-success me-2"></i><?php echo htmlspecialchars($feature); ?>
                                    </li>
                            <?php endforeach; ?>
                                </ul>
                                </div>
                    <?php endif; ?>

                            <?php if (isset($scanResult['expert_analysis']['recommendations']) && !empty($scanResult['expert_analysis']['recommendations'])): ?>
                            <div class="mt-3">
                                <h5><i class="fas fa-lightbulb text-info me-2"></i>Recommendations:</h5>
                                <ul class="list-group">
                                    <?php foreach ($scanResult['expert_analysis']['recommendations'] as $rec): ?>
                                    <li class="list-group-item">
                                        <i class="fas fa-arrow-right text-primary me-2"></i><?php echo htmlspecialchars($rec); ?>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                                </div>
                            <?php endif; ?>
                                </div>
                        <?php endif; ?>

                        <!-- Blacklist Actions -->
                        <div class="mt-4">
                    <?php if ($isPhishing): ?>
                                <div class="alert alert-warning">
                                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Phishing Domain Detected!</h5>
                                    <p>This domain has been automatically added to the blacklist due to high confidence phishing detection.</p>
                        </div>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <button class="btn btn-success btn-lg" onclick="removeFromBlacklist('<?php echo htmlspecialchars($scanResult['url']); ?>')">
                                        <i class="fas fa-undo me-2"></i>Restore Domain
                                    </button>
                    <?php endif; ?>
                            <?php else: ?>
                                <div class="alert alert-success">
                                    <h5><i class="fas fa-check-circle me-2"></i>Safe Domain Confirmed!</h5>
                                    <p>This domain has been verified as safe by our ML model.</p>
                    </div>
                    <?php endif; ?>
                        </div>

                    <!-- Scan Actions -->
                    <div class="scan-actions">
                        <a href="/url_phishing_project/public/scan" class="btn btn-primary">
                            <i class="fas fa-search"></i> Scan Another URL
                        </a>
                                    </div>
                                    </div>
                    <?php
                        }
                    }
                    ?>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                <script>
        document.getElementById('scanForm')?.addEventListener('submit', function(e) {
            // Let the form submit normally to PHP backend with ML model
            const loading = document.querySelector('.loading');
            const resultCard = document.querySelector('.scan-result');
            
            if (loading) loading.style.display = 'block';
            if (resultCard) resultCard.style.display = 'none';
            
            // Smooth scroll to loading
            loading?.scrollIntoView({ behavior: 'smooth' });
                });

                // Add domain to blacklist via AJAX
        function addToBlacklist(url) {
            const domain = new URL(url).hostname;
                    if (confirm('Are you sure you want to add ' + domain + ' to the blacklist?')) {
                        // Show loading state
                        const button = event.target;
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                        button.disabled = true;

                        // Use AJAX to add to blacklist without page redirect
                        fetch('/url_phishing_project/public/admin/blacklist', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: 'action=add&domain=' + encodeURIComponent(domain) + '&reason=Detected as phishing by ML model'
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

        // Remove domain from blacklist
        function removeFromBlacklist(url) {
            const domain = new URL(url).hostname;
            if (confirm('Are you sure you want to remove ' + domain + ' from the blacklist?')) {
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Removing...';
                button.disabled = true;

                fetch('/url_phishing_project/public/admin/blacklist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=remove&domain=' + encodeURIComponent(domain)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Removed from Blacklist';
                        button.className = 'btn btn-secondary btn-lg';
                        button.disabled = true;
                    } else {
                        alert(data.message);
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to remove from blacklist. Please try again.');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

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
    </script>
</body>
</html> 