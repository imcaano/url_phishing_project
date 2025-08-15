<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan URL - URL Phishing Detection</title>
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/style.css">
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .scan-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        .result-box {
            margin-top: 30px;
            padding: 20px;
            border-radius: 8px;
        }
        .result-box.phishing {
            background-color: #ffe6e6;
            border: 1px solid #ffcccc;
        }
        .result-box.safe {
            background-color: #e6ffe6;
            border: 1px solid #ccffcc;
        }
        .confidence-bar {
            height: 20px;
            background-color: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }
        .confidence-fill {
            height: 100%;
            background-color: var(--danger);
            transition: width 0.3s ease;
        }
        .feature-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 10px;
        }
        .feature-item {
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .model-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .model-header {
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
        }
        .model-body {
            padding: 15px;
        }
        .confidence-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .confidence-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 14px;
        }
        .confidence-high {
            background-color: #dc3545;
        }
        .confidence-medium {
            background-color: #ffc107;
            color: #212529;
        }
        .confidence-low {
            background-color: #28a745;
        }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .btn-report {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px;
        }
        .btn-safe {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 5px;
        }
        .ensemble-result {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>URL Scanner</h2>
            </div>
            <nav>
                <a href="/url_phishing_project/public/dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/url_phishing_project/public/predict" class="active">
                    <i class="fas fa-search"></i> Scan URL
                </a>
                <a href="/url_phishing_project/public/report">
                    <i class="fas fa-history"></i> Scan History
                </a>
                <a href="/url_phishing_project/public/blacklist">
                    <i class="fas fa-ban"></i> Domain Blacklist
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/url_phishing_project/public/admin/users">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="/url_phishing_project/public/import">
                    <i class="fas fa-file-import"></i> Import Domains
                </a>
                <?php endif; ?>
                <a href="/url_phishing_project/public/profile">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="/url_phishing_project/public/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>URL Scanner</h1>
            </header>

            <div class="scan-container">
                <div class="card mb-4">
                    <div class="card-body">
                        <h2 class="card-title">Enter URL to Scan</h2>
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        <form id="scanForm" method="post" action="/url_phishing_project/public/predict">
                            <div class="input-group mb-3">
                                <input type="url" name="url" class="form-control" placeholder="https://example.com" required 
                                       value="<?php echo isset($_POST['url']) ? htmlspecialchars($_POST['url']) : ''; ?>">
                                <button type="submit" class="btn btn-primary" id="scanBtn">
                                    <i class="fas fa-search"></i> Scan URL
                                </button>
                            </div>
                        </form>
                        
                        <div id="loading" class="text-center" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Scanning with ML model... Please wait...</p>
                        </div>
                    </div>
                </div>

                <?php if (isset($scanResult) && $scanResult): ?>
                <div class="card <?php echo $scanResult['is_phishing'] ? 'border-danger' : 'border-success'; ?>">
                    <div class="card-header <?php echo $scanResult['is_phishing'] ? 'bg-danger text-white' : 'bg-success text-white'; ?>">
                        <h3 class="mb-0">Scan Results</h3>
                    </div>
                    <div class="card-body">
                        <!-- URL Info -->
                        <div class="mb-4">
                            <h4><i class="fas fa-link"></i> URL Information</h4>
                            <p class="lead"><?php echo htmlspecialchars($scanResult['url']); ?></p>
                        </div>

                        <!-- Ensemble Result -->
                        <?php if (isset($scanResult['ensemble_confidence'])): ?>
                        <div class="ensemble-result">
                            <h4><i class="fas fa-brain"></i> AI Ensemble Analysis</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Final Decision</h5>
                                    <span class="badge <?php echo $scanResult['is_phishing'] ? 'bg-danger' : 'bg-success'; ?> fs-4">
                                        <?php echo $scanResult['is_phishing'] ? 'PHISHING DETECTED' : 'SAFE'; ?>
                                    </span>
                                </div>
                                <div class="col-md-6">
                                    <h5>Ensemble Confidence</h5>
                                    <div class="confidence-circle <?php 
                                        echo $scanResult['ensemble_confidence'] >= 75 ? 'confidence-high' : 
                                            ($scanResult['ensemble_confidence'] >= 50 ? 'confidence-medium' : 'confidence-low'); 
                                    ?>">
                                        <?php echo number_format($scanResult['ensemble_confidence'], 1); ?>%
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Individual Model Results -->
                        <?php if (isset($scanResult['model_predictions'])): ?>
                        <div class="mb-4">
                            <h4><i class="fas fa-cogs"></i> Individual Model Results</h4>
                            <div class="row">
                                <?php 
                                $models = [
                                    'svm' => 'Support Vector Machine',
                                    'decision_tree' => 'Decision Tree',
                                    'neural_network' => 'Neural Network'
                                ];
                                foreach ($models as $key => $name): 
                                    if (isset($scanResult['model_predictions'][$key])):
                                        $model = $scanResult['model_predictions'][$key];
                                ?>
                                <div class="col-md-4">
                                    <div class="model-card">
                                        <div class="model-header">
                                            <i class="fas fa-robot"></i> <?php echo $name; ?>
                                        </div>
                                        <div class="model-body">
                                            <div class="confidence-indicator">
                                                <div class="confidence-circle <?php 
                                                    echo $model['confidence'] >= 75 ? 'confidence-high' : 
                                                        ($model['confidence'] >= 50 ? 'confidence-medium' : 'confidence-low'); 
                                                ?>">
                                                    <?php echo number_format($model['confidence'], 1); ?>%
                                                </div>
                                                <div>
                                                    <strong>Prediction:</strong><br>
                                                    <span class="badge <?php echo $model['prediction'] ? 'bg-danger' : 'bg-success'; ?>">
                                                        <?php echo $model['prediction'] ? 'Phishing' : 'Safe'; ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php 
                                        endif;
                                    endforeach; 
                                ?>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Status -->
                        <div class="mb-4">
                            <h4><i class="fas fa-shield-alt"></i> Status</h4>
                            <span class="badge <?php echo $scanResult['is_phishing'] ? 'bg-danger' : 'bg-success'; ?> fs-5">
                                <?php echo $scanResult['is_phishing'] ? 'Potential Phishing' : 'Safe'; ?>
                            </span>
                        </div>

                        <!-- Risk Score -->
                        <div class="mb-4">
                            <h4><i class="fas fa-chart-line"></i> Risk Score</h4>
                            <div class="progress">
                                <div class="progress-bar <?php 
                                    echo $scanResult['confidence_score'] >= 75 ? 'bg-danger' : 
                                        ($scanResult['confidence_score'] >= 50 ? 'bg-warning' : 'bg-success'); 
                                ?>" 
                                role="progressbar" 
                                style="width: <?php echo $scanResult['confidence_score']; ?>%">
                                    <?php echo number_format($scanResult['confidence_score'], 1); ?>%
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <?php if ($scanResult['is_phishing']): ?>
                                <button class="btn btn-report" onclick="reportPhishing()">
                                    <i class="fas fa-flag"></i> Report as Phishing
                                </button>
                            <?php else: ?>
                                <button class="btn btn-safe" onclick="markAsSafe()">
                                    <i class="fas fa-check-circle"></i> Mark as Safe
                                </button>
                            <?php endif; ?>
                            <button class="btn btn-secondary" onclick="scanAnother()">
                                <i class="fas fa-search"></i> Scan Another URL
                            </button>
                        </div>

                        <!-- Features Analysis -->
                        <div class="row mt-4">
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
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('scanForm').addEventListener('submit', function(e) {
            // Let the form submit normally to PHP backend with ML model
            const loading = document.getElementById('loading');
            if (loading) loading.style.display = 'block';
        });





        function addToBlacklist(url) {
            if (confirm('Are you sure you want to add this URL to the blacklist?')) {
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
                button.disabled = true;

                fetch('/url_phishing_project/public/admin/blacklist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=add&domain=' + encodeURIComponent(new URL(url).hostname) + '&reason=Added from ML scan'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Added to Blacklist';
                        button.className = 'btn btn-success';
                        button.disabled = true;
                    } else {
                        alert(data.message);
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add to blacklist. Please try again.');
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        function removeFromBlacklist(url) {
            if (confirm('Are you sure you want to remove this URL from the blacklist?')) {
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
                    body: 'action=remove&domain=' + encodeURIComponent(new URL(url).hostname)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        button.innerHTML = '<i class="fas fa-check me-2"></i>Removed from Blacklist';
                        button.className = 'btn btn-secondary';
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

        function reportPhishing() {
            if (confirm('Are you sure you want to report this URL as phishing?')) {
                // Add logic to report phishing
                alert('URL reported as phishing. Thank you for helping keep the web safe!');
            }
        }

        function markAsSafe() {
            if (confirm('Are you sure this URL is safe?')) {
                // Add logic to mark as safe
                alert('URL marked as safe. Thank you for the feedback!');
            }
        }

        function scanAnother() {
            document.getElementById('scanForm').reset();
            location.reload();
        }
    </script>
</body>
</html> 