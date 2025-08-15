<?php
session_start();
require_once '../backend/config/Database.php';
require_once '../backend/controllers/URLScanController.php';

$message = '';
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['url'])) {
    $url = $_POST['url'];
    
    // Add http:// if no protocol specified
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    
    try {
        $controller = new App\Controllers\URLScanController();
        $result = $controller->scanWithML($url);
        
        if (isset($result['error'])) {
            $message = 'Error: ' . $result['error'];
        } else {
            $message = 'Scan completed successfully!';
        }
    } catch (Exception $e) {
        $message = 'Exception: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ML Model Test - URL Phishing Detection</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background-color: #3498db;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .success {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        .result {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .result h3 {
            margin-top: 0;
            color: #495057;
        }
        .url-examples {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .url-examples h4 {
            margin-top: 0;
            color: #856404;
        }
        .url-examples ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .url-examples li {
            margin: 5px 0;
            font-family: monospace;
        }
        .prediction {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
        }
        .prediction.safe {
            background-color: #d4edda;
            color: #155724;
        }
        .prediction.phishing {
            background-color: #f8d7da;
            color: #721c24;
        }
        .confidence {
            text-align: center;
            font-size: 18px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔒 ML Model Test - URL Phishing Detection</h1>
        
        <div class="url-examples">
            <h4>📋 Test URL Examples:</h4>
            <ul>
                <li><strong>Safe URLs:</strong> https://www.google.com, https://www.github.com, https://www.wikipedia.org</li>
                <li><strong>Phishing URLs:</strong> http://paypal.com-login-security-alert.com, http://update-appleid.com-login.verify-user.com</li>
                <li><strong>Suspicious URLs:</strong> https://example-suspicious-site.tk, https://fake-login-site.xyz</li>
            </ul>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="url">Enter URL to scan:</label>
                <input type="text" id="url" name="url" placeholder="https://www.google.com" value="<?php echo htmlspecialchars($_POST['url'] ?? ''); ?>" required>
            </div>
            <button type="submit">🔍 Scan URL with ML Model</button>
        </form>

        <?php if ($message): ?>
            <div class="message <?php echo isset($result['error']) ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($result && !isset($result['error'])): ?>
            <div class="result">
                <h3>📊 ML Model Results</h3>
                
                <div class="prediction <?php echo $result['prediction']; ?>">
                    <?php if ($result['prediction'] === 'phishing'): ?>
                        🚨 PHISHING DETECTED
                    <?php else: ?>
                        ✅ SAFE
                    <?php endif; ?>
                </div>
                
                <div class="confidence">
                    Confidence Score: <strong><?php echo $result['confidence_score']; ?>%</strong>
                </div>
                
                <?php if (isset($result['risk_level'])): ?>
                    <p><strong>Risk Level:</strong> <?php echo htmlspecialchars($result['risk_level']); ?></p>
                <?php endif; ?>
                
                <?php if (isset($result['expert_analysis'])): ?>
                    <h4>🧠 Expert Analysis:</h4>
                    <?php if (isset($result['expert_analysis']['summary'])): ?>
                        <p><strong>Summary:</strong> <?php echo htmlspecialchars($result['expert_analysis']['summary']); ?></p>
                    <?php endif; ?>
                    
                    <?php if (isset($result['expert_analysis']['risk_factors']) && !empty($result['expert_analysis']['risk_factors'])): ?>
                        <p><strong>Risk Factors:</strong></p>
                        <ul>
                            <?php foreach ($result['expert_analysis']['risk_factors'] as $factor): ?>
                                <li><?php echo htmlspecialchars($factor); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                    
                    <?php if (isset($result['expert_analysis']['recommendations']) && !empty($result['expert_analysis']['recommendations'])): ?>
                        <p><strong>Recommendations:</strong></p>
                        <ul>
                            <?php foreach ($result['expert_analysis']['recommendations'] as $recommendation): ?>
                                <li><?php echo htmlspecialchars($recommendation); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if (isset($result['features'])): ?>
                    <h4>🔍 Technical Features:</h4>
                    <pre><?php echo json_encode($result['features'], JSON_PRETTY_PRINT); ?></pre>
                <?php endif; ?>
                
                <h4>📋 Full API Response:</h4>
                <pre><?php echo json_encode($result, JSON_PRETTY_PRINT); ?></pre>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px; text-align: center; color: #666;">
            <p><strong>ML Model Info:</strong> GradientBoosting Classifier with 96.87% accuracy</p>
            <p><strong>API Status:</strong> Running on http://localhost:5000</p>
        </div>
    </div>
</body>
</html>
