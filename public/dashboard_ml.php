<?php
session_start();
require_once '../backend/config/Database.php';
require_once '../backend/controllers/URLScanController.php';
require_once '../backend/controllers/AdminController.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$userId = $_SESSION['user_id'];
$controller = new App\Controllers\URLScanController();
$adminController = new App\Controllers\AdminController();

// Get recent scans
$recentScans = $controller->getReports(['limit' => 10]);
$stats = $controller->getScannedDomainStats();
$blacklistDomains = $adminController->getBlacklistedDomains();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ML-Enhanced Dashboard - URL Phishing Detection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 2.5em;
        }
        .header p {
            margin: 10px 0 0 0;
            font-size: 1.2em;
            opacity: 0.9;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card h3 {
            margin: 0 0 15px 0;
            color: #333;
            font-size: 1.1em;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: bold;
            color: #667eea;
        }
        .stat-card .label {
            color: #666;
            margin-top: 5px;
        }
        .scan-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .scan-section h2 {
            margin-top: 0;
            color: #333;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .url-input {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }
        .url-input input {
            flex: 1;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .url-input input:focus {
            border-color: #667eea;
            outline: none;
        }
        .scan-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        .scan-btn:hover {
            background: #5a6fd8;
        }
        .scan-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .results {
            margin-top: 20px;
        }
        .result-card {
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            border-left: 5px solid;
        }
        .result-card.safe {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        .result-card.phishing {
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .prediction {
            font-size: 1.5em;
            font-weight: bold;
        }
        .confidence {
            background: rgba(255,255,255,0.8);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
        }
        .blacklist-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(0,0,0,0.1);
        }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            margin-right: 10px;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        .recent-scans {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .scan-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid #eee;
        }
        .scan-item:last-child {
            border-bottom: none;
        }
        .scan-info {
            flex: 1;
        }
        .scan-url {
            font-weight: bold;
            color: #333;
        }
        .scan-result {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .scan-result.safe {
            background: #d4edda;
            color: #155724;
        }
        .scan-result.phishing {
            background: #f8d7da;
            color: #721c24;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }
        .ml-badge {
            background: #17a2b8;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔒 ML-Enhanced Phishing Detection Dashboard</h1>
            <p>Powered by Machine Learning Model with 96.87% Accuracy</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Scans</h3>
                <div class="number"><?php echo $stats['total_scans']; ?></div>
                <div class="label">URLs Analyzed</div>
            </div>
            <div class="stat-card">
                <h3>Phishing Detected</h3>
                <div class="number"><?php echo $stats['phishing_domains']; ?></div>
                <div class="label">Threats Blocked</div>
            </div>
            <div class="stat-card">
                <h3>Blacklisted</h3>
                <div class="number"><?php echo count($blacklistDomains); ?></div>
                <div class="label">Domains Blocked</div>
            </div>
            <div class="stat-card">
                <h3>ML Accuracy</h3>
                <div class="number">96.87%</div>
                <div class="label">Model Performance</div>
            </div>
        </div>

        <div class="scan-section">
            <h2>🔍 Scan New URL with ML Model</h2>
            <div class="url-input">
                <input type="text" id="urlInput" placeholder="Enter URL to scan (e.g., https://www.google.com)" />
                <button class="scan-btn" id="scanBtn" onclick="scanURL()">🔍 Scan URL</button>
            </div>
            <div id="loading" class="loading" style="display: none;">
                Scanning URL with ML model... Please wait...
            </div>
            <div id="results" class="results"></div>
        </div>

        <div class="recent-scans">
            <h2>📋 Recent Scans</h2>
            <?php if (empty($recentScans)): ?>
                <p>No recent scans found.</p>
            <?php else: ?>
                <?php foreach ($recentScans as $scan): ?>
                    <div class="scan-item">
                        <div class="scan-info">
                            <div class="scan-url"><?php echo htmlspecialchars($scan['url']); ?></div>
                            <div style="margin-top: 5px; color: #666; font-size: 14px;">
                                Scanned: <?php echo date('M j, Y H:i', strtotime($scan['scan_date'])); ?>
                                <?php if (isset($scan['ml_source']) && $scan['ml_source']): ?>
                                    <span class="ml-badge">ML</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="scan-result <?php echo $scan['is_phishing'] ? 'phishing' : 'safe'; ?>">
                            <?php echo $scan['is_phishing'] ? '🚨 PHISHING' : '✅ SAFE'; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        async function scanURL() {
            const url = document.getElementById('urlInput').value.trim();
            if (!url) {
                alert('Please enter a URL to scan');
                return;
            }

            // Show loading
            document.getElementById('loading').style.display = 'block';
            document.getElementById('results').innerHTML = '';
            document.getElementById('scanBtn').disabled = true;

            try {
                // Try ML API first
                const response = await fetch('http://localhost:5000/predict', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ url: url })
                });

                if (response.ok) {
                    const data = await response.json();
                    displayMLResult(data);
                } else {
                    // Fallback to PHP API
                    const phpResponse = await fetch('../backend/api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=scan&url=${encodeURIComponent(url)}`
                    });
                    
                    if (phpResponse.ok) {
                        const phpData = await phpResponse.json();
                        displayPHPResult(phpData);
                    } else {
                        throw new Error('Both ML API and PHP API failed');
                    }
                }
            } catch (error) {
                document.getElementById('results').innerHTML = `
                    <div class="error">
                        <strong>Error:</strong> Could not scan URL. Please ensure the ML API is running on http://localhost:5000
                    </div>
                `;
            } finally {
                document.getElementById('loading').style.display = 'none';
                document.getElementById('scanBtn').disabled = false;
            }
        }

        function displayMLResult(data) {
            const isPhishing = data.prediction === 'phishing';
            const resultClass = isPhishing ? 'phishing' : 'safe';
            const predictionText = isPhishing ? '🚨 PHISHING DETECTED' : '✅ SAFE';
            
            let blacklistActions = '';
            if (isPhishing) {
                blacklistActions = `
                    <div class="blacklist-actions">
                        <span class="ml-badge">ML Model Result</span>
                        <button class="btn btn-danger" onclick="addToBlacklist('${data.url}')">Add to Blacklist</button>
                    </div>
                `;
            } else {
                blacklistActions = `
                    <div class="blacklist-actions">
                        <span class="ml-badge">ML Model Result</span>
                        <button class="btn btn-success" onclick="removeFromBlacklist('${data.url}')">Remove from Blacklist</button>
                    </div>
                `;
            }

            document.getElementById('results').innerHTML = `
                <div class="result-card ${resultClass}">
                    <div class="result-header">
                        <div class="prediction">${predictionText}</div>
                        <div class="confidence">${data.confidence_score}% Confidence</div>
                    </div>
                    <div><strong>Risk Level:</strong> ${data.risk_level}</div>
                    ${data.expert_analysis ? `
                        <div style="margin-top: 15px;">
                            <strong>Expert Analysis:</strong><br>
                            ${data.expert_analysis.summary}
                        </div>
                    ` : ''}
                    ${blacklistActions}
                </div>
            `;
        }

        function displayPHPResult(data) {
            // Handle PHP API results
            const isPhishing = data.is_phishing;
            const resultClass = isPhishing ? 'phishing' : 'safe';
            const predictionText = isPhishing ? '🚨 PHISHING DETECTED' : '✅ SAFE';
            
            document.getElementById('results').innerHTML = `
                <div class="result-card ${resultClass}">
                    <div class="result-header">
                        <div class="prediction">${predictionText}</div>
                        <div class="confidence">${data.confidence_score || 'N/A'}% Confidence</div>
                    </div>
                    <div><strong>Source:</strong> PHP Fallback System</div>
                </div>
            `;
        }

        async function addToBlacklist(url) {
            try {
                const response = await fetch('../backend/api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=addToBlacklist&url=${encodeURIComponent(url)}`
                });
                
                if (response.ok) {
                    alert('URL added to blacklist successfully!');
                    location.reload();
                } else {
                    alert('Failed to add to blacklist');
                }
            } catch (error) {
                alert('Error adding to blacklist: ' + error.message);
            }
        }

        async function removeFromBlacklist(url) {
            try {
                const response = await fetch('../backend/api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=removeFromBlacklist&url=${encodeURIComponent(url)}`
                });
                
                if (response.ok) {
                    alert('URL removed from blacklist successfully!');
                    location.reload();
                } else {
                    alert('Failed to remove from blacklist');
                }
            } catch (error) {
                alert('Error removing from blacklist: ' + error.message);
            }
        }

        // Enter key to scan
        document.getElementById('urlInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                scanURL();
            }
        });
    </script>
</body>
</html>

