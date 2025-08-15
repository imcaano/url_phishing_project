# Machine Learning Integration Guide

This guide explains how to integrate the advanced machine learning phishing detection system with your existing PHP application.

## 🚀 Quick Start

### 1. Train the ML Model

Navigate to the ML directory and run the training script:

**Windows:**
```bash
cd backend/ml
start_training.bat
```

**Linux/Mac:**
```bash
cd backend/ml
./start_training.sh
```

### 2. Start the ML API

The training script will automatically start the API, or you can start it manually:

**Windows:**
```bash
start_api.bat
```

**Linux/Mac:**
```bash
./start_api.sh
```

### 3. Integrate with PHP

Use the `MLPredictor` class in your PHP application:

```php
require_once 'backend/models/MLPredictor.php';

$predictor = new MLPredictor();

// Make predictions
$result = $predictor->predictWithFallback('https://example.com');
```

## 🔧 Integration Options

### Option 1: Direct ML API Integration (Recommended)

Use the trained ML model for highest accuracy:

```php
$predictor = new MLPredictor();

// Check if ML API is available
if ($predictor->isApiAvailable()) {
    $result = $predictor->predictUrl($url);
} else {
    // Fallback to traditional analysis
    $result = $predictor->predictWithFallback($url);
}
```

### Option 2: Fallback-Only Mode

Use traditional feature-based analysis when ML is unavailable:

```php
$predictor = new MLPredictor();
$result = $predictor->predictWithFallback($url);
```

### Option 3: Batch Processing

Process multiple URLs efficiently:

```php
$urls = ['https://example1.com', 'https://example2.com'];
$results = $predictor->predictBatch($urls);
```

## 📊 Response Format

The system returns comprehensive results:

```php
$result = [
    'url' => 'https://example.com',
    'prediction' => 'phishing', // or 'safe'
    'confidence_score' => 95.67,
    'risk_level' => 'VERY_HIGH', // VERY_HIGH, HIGH, MEDIUM, LOW, VERY_LOW
    'confidence_scores' => [
        'safe' => 4.33,
        'phishing' => 95.67
    ],
    'features' => [
        'URL_Length' => 23,
        'Domain_Length' => 11,
        'Entropy_Score' => 3.45,
        // ... 23 total features
    ],
    'expert_analysis' => [
        'summary' => '🚨 PHISHING DETECTED...',
        'risk_factors' => ['Multiple subdomains detected'],
        'security_features' => ['HTTPS encryption enabled'],
        'recommendations' => ['Do not visit this URL...'],
        'technical_details' => [
            'domain' => 'example.com',
            'suspicious_features_count' => 3
        ]
    ],
    'model_info' => [
        'model_type' => 'RandomForestClassifier',
        'accuracy' => 0.9456,
        'training_date' => '2025-08-01T22:30:00'
    ],
    'timestamp' => '2025-08-01T22:30:00',
    'status' => 'success'
];
```

## 🔌 Integration Examples

### Example 1: URL Scan Controller Integration

Update your existing `URLScanController.php`:

```php
<?php
require_once __DIR__ . '/../models/MLPredictor.php';

class URLScanController {
    private $mlPredictor;
    
    public function __construct() {
        $this->mlPredictor = new MLPredictor();
    }
    
    public function scan() {
        $url = $_POST['url'] ?? '';
        
        if (empty($url)) {
            return ['error' => 'URL is required'];
        }
        
        // Use ML prediction with fallback
        $result = $this->mlPredictor->predictWithFallback($url);
        
        if ($result['status'] === 'success') {
            // Save scan result to database
            $this->saveScanResult($result);
            
            return [
                'success' => true,
                'result' => $result
            ];
        } else {
            return [
                'error' => 'Failed to analyze URL: ' . ($result['error'] ?? 'Unknown error')
            ];
        }
    }
    
    private function saveScanResult($result) {
        // Your existing database save logic
        // Enhanced with ML results
    }
}
```

### Example 2: API Integration

Update your `api.php`:

```php
<?php
require_once __DIR__ . '/../backend/models/MLPredictor.php';

header('Content-Type: application/json');

$predictor = new MLPredictor();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $url = $input['url'] ?? '';
    
    if (empty($url)) {
        http_response_code(400);
        echo json_encode(['error' => 'URL parameter required']);
        exit;
    }
    
    $result = $predictor->predictWithFallback($url);
    echo json_encode($result);
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
```

### Example 3: Admin Dashboard Integration

Enhance your admin dashboard with ML insights:

```php
<?php
require_once __DIR__ . '/../models/MLPredictor.php';

class AdminController {
    private $mlPredictor;
    
    public function __construct() {
        $this->mlPredictor = new MLPredictor();
    }
    
    public function dashboard() {
        // Get ML model information
        $modelInfo = $this->mlPredictor->getModelInfo();
        
        // Get system statistics
        $stats = [
            'ml_accuracy' => $modelInfo['model_info']['accuracy'] ?? 'N/A',
            'model_type' => $modelInfo['model_info']['model_type'] ?? 'N/A',
            'training_date' => $modelInfo['model_info']['training_date'] ?? 'N/A',
            'api_status' => $this->mlPredictor->isApiAvailable() ? 'Online' : 'Offline'
        ];
        
        // Include in your dashboard view
        include '../views/admin/dashboard.php';
    }
}
```

## 🎯 Enhanced URL Scanning

### Confidence-Based Decisions

Use confidence scores for better decision making:

```php
$result = $predictor->predictWithFallback($url);

if ($result['confidence_score'] >= 90) {
    // High confidence - take immediate action
    if ($result['prediction'] === 'phishing') {
        // Auto-blacklist high-confidence phishing URLs
        $this->autoBlacklist($url, $result);
    }
} elseif ($result['confidence_score'] >= 70) {
    // Medium confidence - flag for review
    $this->flagForReview($url, $result);
} else {
    // Low confidence - use additional verification
    $result = $this->additionalVerification($url, $result);
}
```

### Risk Level Handling

Implement different actions based on risk levels:

```php
switch ($result['risk_level']) {
    case 'VERY_HIGH':
        $this->immediateBlock($url);
        $this->sendAlert($url, 'VERY_HIGH');
        break;
        
    case 'HIGH':
        $this->blockWithReview($url);
        break;
        
    case 'MEDIUM':
        $this->flagForMonitoring($url);
        break;
        
    case 'LOW':
        $this->allowWithWarning($url);
        break;
        
    case 'VERY_LOW':
        $this->allow($url);
        break;
}
```

## 🔍 Expert Analysis Integration

### Display Detailed Analysis

Show comprehensive analysis to users:

```php
function displayExpertAnalysis($result) {
    $analysis = $result['expert_analysis'];
    
    echo '<div class="expert-analysis">';
    echo '<h3>AI Expert Analysis</h3>';
    echo '<p>' . $analysis['summary'] . '</p>';
    
    if (!empty($analysis['risk_factors'])) {
        echo '<h4>🚨 Risk Factors:</h4>';
        echo '<ul>';
        foreach ($analysis['risk_factors'] as $factor) {
            echo '<li>' . htmlspecialchars($factor) . '</li>';
        }
        echo '</ul>';
    }
    
    if (!empty($analysis['security_features'])) {
        echo '<h4>🔒 Security Features:</h4>';
        echo '<ul>';
        foreach ($analysis['security_features'] as $feature) {
            echo '<li>' . htmlspecialchars($feature) . '</li>';
        }
        echo '</ul>';
    }
    
    if (!empty($analysis['recommendations'])) {
        echo '<h4>💡 Recommendations:</h4>';
        echo '<ul>';
        foreach ($analysis['recommendations'] as $rec) {
            echo '<li>' . htmlspecialchars($rec) . '</li>';
        }
        echo '</ul>';
    }
    
    echo '</div>';
}
```

### Feature Visualization

Show technical details to advanced users:

```php
function displayTechnicalDetails($result) {
    $features = $result['features'];
    $tech = $result['expert_analysis']['technical_details'];
    
    echo '<div class="technical-details">';
    echo '<h4>🔬 Technical Analysis</h4>';
    echo '<table class="feature-table">';
    echo '<tr><th>Feature</th><th>Value</th><th>Risk Level</th></tr>';
    
    foreach ($features as $feature => $value) {
        $riskClass = $this->getFeatureRiskClass($feature, $value);
        echo "<tr class='$riskClass'>";
        echo '<td>' . htmlspecialchars($feature) . '</td>';
        echo '<td>' . htmlspecialchars($value) . '</td>';
        echo '<td>' . $this->getFeatureRiskLabel($feature, $value) . '</td>';
        echo '</tr>';
    }
    
    echo '</table>';
    echo '</div>';
}
```

## 🚨 Error Handling

### Graceful Fallback

Handle ML API failures gracefully:

```php
try {
    $result = $predictor->predictUrl($url);
    
    if ($result['status'] === 'success') {
        return $result;
    } else {
        // Log the error
        error_log("ML API error: " . ($result['error'] ?? 'Unknown error'));
        
        // Use fallback analysis
        return $predictor->predictWithFallback($url);
    }
} catch (Exception $e) {
    // Log the exception
    error_log("ML prediction exception: " . $e->getMessage());
    
    // Use fallback analysis
    return $predictor->predictWithFallback($url);
}
```

### Health Monitoring

Monitor ML API health:

```php
function checkMLSystemHealth() {
    $predictor = new MLPredictor();
    
    $health = [
        'api_available' => $predictor->isApiAvailable(),
        'model_info' => $predictor->getModelInfo(),
        'last_check' => date('Y-m-d H:i:s')
    ];
    
    // Store health status for monitoring
    $this->updateSystemHealth($health);
    
    return $health;
}
```

## 📈 Performance Optimization

### Caching Results

Cache prediction results for repeated URLs:

```php
function getCachedPrediction($url) {
    $cacheKey = 'prediction_' . md5($url);
    
    // Check cache first
    $cached = $this->cache->get($cacheKey);
    if ($cached) {
        return $cached;
    }
    
    // Make new prediction
    $result = $this->predictor->predictWithFallback($url);
    
    // Cache for 1 hour
    $this->cache->set($cacheKey, $result, 3600);
    
    return $result;
}
```

### Batch Processing

Process multiple URLs efficiently:

```php
function processBatchUrls($urls) {
    // Use batch prediction if available
    if (count($urls) > 1) {
        $results = $this->predictor->predictBatch($urls);
        return $results['results'];
    }
    
    // Process individually
    $results = [];
    foreach ($urls as $url) {
        $results[] = $this->predictor->predictWithFallback($url);
    }
    
    return $results;
}
```

## 🔧 Configuration

### Customize ML API Settings

```php
// In your configuration file
$mlConfig = [
    'api_url' => 'http://localhost:5000',
    'timeout' => 30,
    'max_retries' => 3,
    'fallback_threshold' => 0.7, // Confidence threshold for fallback
    'auto_blacklist_threshold' => 0.9, // Auto-blacklist threshold
    'cache_duration' => 3600 // Cache duration in seconds
];

// Initialize predictor with custom config
$predictor = new MLPredictor();
$predictor->setApiUrl($mlConfig['api_url']);
```

## 🧪 Testing

### Test the Integration

```php
function testMLIntegration() {
    $predictor = new MLPredictor();
    
    $testUrls = [
        'https://www.google.com', // Should be safe
        'https://fake-login-site.tk', // Should be phishing
        'https://suspicious-banking.xyz' // Should be phishing
    ];
    
    foreach ($testUrls as $url) {
        echo "Testing: $url\n";
        $result = $predictor->predictWithFallback($url);
        
        if ($result['status'] === 'success') {
            echo "Result: " . $result['prediction'] . "\n";
            echo "Confidence: " . $result['confidence_score'] . "%\n";
            echo "Risk Level: " . $result['risk_level'] . "\n";
        } else {
            echo "Error: " . ($result['error'] ?? 'Unknown error') . "\n";
        }
        echo "---\n";
    }
}
```

## 📊 Monitoring and Analytics

### Track ML Performance

```php
function trackMLPerformance($url, $result, $userAction) {
    $metrics = [
        'url' => $url,
        'prediction' => $result['prediction'],
        'confidence' => $result['confidence_score'],
        'risk_level' => $result['risk_level'],
        'user_action' => $userAction, // 'blocked', 'allowed', 'reviewed'
        'timestamp' => date('Y-m-d H:i:s'),
        'model_type' => $result['model_info']['model_type'] ?? 'Unknown'
    ];
    
    // Store metrics for analysis
    $this->saveMLMetrics($metrics);
}
```

## 🚀 Deployment Checklist

- [ ] Python 3.8+ installed
- [ ] ML dependencies installed (`pip install -r requirements.txt`)
- [ ] Training datasets available (`phishing.csv`, `safe.csv`)
- [ ] Model trained successfully
- [ ] Flask API running on port 5000
- [ ] PHP integration class included
- [ ] Error handling implemented
- [ ] Fallback system tested
- [ ] Performance monitoring configured
- [ ] User interface updated

## 🆘 Troubleshooting

### Common Issues

1. **ML API not responding**
   - Check if Flask API is running
   - Verify port 5000 is available
   - Check API logs for errors

2. **Model not loading**
   - Ensure `phishing_model.joblib` exists
   - Check file permissions
   - Verify Python dependencies

3. **Low accuracy**
   - Retrain with more data
   - Check feature extraction
   - Verify training data quality

4. **Performance issues**
   - Enable caching
   - Use batch processing
   - Optimize feature extraction

## 📚 Next Steps

1. **Train the model** with your datasets
2. **Test the API** with sample URLs
3. **Integrate with PHP** using the provided examples
4. **Customize features** based on your needs
5. **Monitor performance** and retrain as needed
6. **Expand training data** for better accuracy

---

This integration provides you with a state-of-the-art phishing detection system that combines machine learning accuracy with traditional security analysis for maximum protection.
