# Advanced Phishing Detection Machine Learning System

This directory contains a comprehensive machine learning system for detecting phishing URLs with high accuracy and confidence scoring.

## 🚀 Features

- **Advanced ML Model**: Random Forest and Gradient Boosting classifiers
- **Comprehensive Feature Extraction**: 23+ URL features including entropy, typosquatting, and homograph detection
- **High Accuracy**: Expected 90%+ accuracy on phishing detection
- **Real-time API**: Flask-based REST API for instant predictions
- **Confidence Scoring**: Detailed confidence levels and risk assessment
- **Fallback System**: Traditional analysis when ML API is unavailable
- **Expert Analysis**: Detailed explanations and recommendations

## 📁 File Structure

```
backend/ml/
├── train_model.py          # Main training script
├── predict_api.py          # Flask API for predictions
├── train_and_deploy.py     # Complete training & deployment script
├── requirements.txt        # Python dependencies
├── README.md              # This file
├── phishing.csv           # Training data (phishing URLs)
├── safe.csv               # Training data (safe URLs)
├── phishing_model.joblib  # Trained model (generated after training)
├── model_info.json        # Model performance metrics (generated after training)
├── feature_importance.png # Feature importance visualization (generated after training)
├── start_api.bat          # Windows startup script (generated)
├── start_api.sh           # Linux/Mac startup script (generated)
└── config.json            # API configuration (generated)
```

## 🛠️ Installation & Setup

### 1. Prerequisites

- Python 3.8 or higher
- pip package manager
- Training datasets (phishing.csv and safe.csv)

### 2. Install Dependencies

```bash
cd backend/ml
pip install -r requirements.txt
```

### 3. Verify Training Data

Ensure you have the training datasets:
- `phishing.csv` - Contains known phishing URLs
- `safe.csv` - Contains legitimate URLs

## 🎯 Training the Model

### Option 1: Complete Training & Deployment (Recommended)

```bash
python train_and_deploy.py
```

This script will:
- Check and install dependencies
- Train the ML model
- Create configuration files
- Start the Flask API
- Test the system
- Create startup scripts

### Option 2: Manual Training

```bash
# Train the model only
python train_model.py

# Start the API separately
python predict_api.py
```

## 🔧 Model Features

The system extracts 23 comprehensive features from URLs:

### Basic URL Features
- URL length, domain length, path length, query length
- Number of dots in domain
- IP address presence
- @ symbol presence
- HTTPS usage

### Advanced Security Features
- Multiple subdomains detection
- Hexadecimal character detection
- Special character analysis
- URL depth analysis
- Random string detection

### Threat Detection
- Suspicious TLD detection (.tk, .ml, .ga, .cf, .gq, .xyz, .top, .club, .site, .online)
- Brand name impersonation detection
- Suspicious keyword counting
- Entropy calculation for randomness
- Redirect and shortener detection
- Typosquatting detection
- Homograph attack detection

## 🌐 API Endpoints

### Health Check
```bash
GET /health
```

### Single URL Prediction
```bash
POST /predict
Content-Type: application/json

{
    "url": "https://example.com"
}
```

### Batch Prediction
```bash
POST /batch_predict
Content-Type: application/json

{
    "urls": [
        "https://example1.com",
        "https://example2.com"
    ]
}
```

### Model Information
```bash
GET /model_info
```

## 📊 API Response Format

```json
{
    "url": "https://example.com",
    "prediction": "phishing",
    "confidence_score": 95.67,
    "risk_level": "VERY_HIGH",
    "confidence_scores": {
        "safe": 4.33,
        "phishing": 95.67
    },
    "features": {
        "URL_Length": 23,
        "Domain_Length": 11,
        "Entropy_Score": 3.45
    },
    "expert_analysis": {
        "summary": "🚨 PHISHING DETECTED...",
        "risk_factors": ["Multiple subdomains detected"],
        "security_features": ["HTTPS encryption enabled"],
        "recommendations": ["Do not visit this URL..."],
        "technical_details": {
            "domain": "example.com",
            "suspicious_features_count": 3
        }
    },
    "model_info": {
        "model_type": "RandomForestClassifier",
        "accuracy": 0.9456,
        "training_date": "2025-08-01T22:30:00"
    },
    "timestamp": "2025-08-01T22:30:00",
    "status": "success"
}
```

## 🔌 PHP Integration

The system includes a PHP integration class (`MLPredictor.php`) that can be used in your existing PHP application:

```php
require_once 'backend/models/MLPredictor.php';

$predictor = new MLPredictor();

// Single prediction
$result = $predictor->predictUrl('https://example.com');

// With fallback
$result = $predictor->predictWithFallback('https://example.com');

// Batch prediction
$results = $predictor->predictBatch(['https://example1.com', 'https://example2.com']);
```

## 🚀 Starting the API

### Windows
```bash
start_api.bat
```

### Linux/Mac
```bash
./start_api.sh
```

### Manual
```bash
python predict_api.py
```

## 📈 Model Performance

Expected performance metrics:
- **Accuracy**: 90-95%
- **Precision**: 88-93%
- **Recall**: 89-94%
- **F1-Score**: 89-93%

## 🔍 Testing the System

### Test URLs
- **Safe**: https://www.google.com, https://www.youtube.com
- **Phishing**: https://fake-login-site.tk, https://suspicious-banking.xyz

### API Testing
```bash
# Health check
curl http://localhost:5000/health

# Single prediction
curl -X POST http://localhost:5000/predict \
  -H "Content-Type: application/json" \
  -d '{"url": "https://www.google.com"}'
```

## 🛡️ Security Features

- Input validation and sanitization
- Rate limiting (configurable)
- Error handling without information leakage
- Secure HTTP headers
- CORS configuration

## 🔧 Configuration

Edit `config.json` to customize:
- API host and port
- Model timeout settings
- Logging levels
- Feature selection parameters

## 📝 Logging

The system provides comprehensive logging:
- API access logs
- Model prediction logs
- Error and warning logs
- Performance metrics

## 🚨 Troubleshooting

### Common Issues

1. **Port 5000 already in use**
   - Change port in `config.json`
   - Kill existing process: `lsof -ti:5000 | xargs kill -9`

2. **Model not loading**
   - Ensure `phishing_model.joblib` exists
   - Check file permissions
   - Verify Python dependencies

3. **Training data issues**
   - Verify CSV format
   - Check file encoding (UTF-8)
   - Ensure sufficient data samples

### Debug Mode

Enable debug mode in `config.json`:
```json
{
    "api": {
        "debug": true
    }
}
```

## 📚 Advanced Usage

### Custom Feature Extraction
Modify `extract_features()` method in `train_model.py` to add custom features.

### Model Tuning
Adjust hyperparameters in `train_model.py`:
- Random Forest parameters
- Gradient Boosting parameters
- Feature selection threshold

### Ensemble Methods
The system automatically selects the best performing model from multiple algorithms.

## 🤝 Contributing

To improve the system:
1. Add new features to feature extraction
2. Implement additional ML algorithms
3. Enhance the expert analysis system
4. Improve the fallback analysis
5. Add more training data

## 📄 License

This project is part of the URL Phishing Detection System.

## 🆘 Support

For issues and questions:
1. Check the troubleshooting section
2. Review the logs in the console
3. Verify all dependencies are installed
4. Ensure training data is properly formatted

---

**Note**: This ML system provides high-accuracy phishing detection but should be used as part of a comprehensive security strategy. Always verify results and maintain updated training data for optimal performance.
