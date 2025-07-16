URL Phishing Detection Project Documentation
=========================================

Project Overview
---------------
This is a web-based URL phishing detection system that helps users identify potentially malicious URLs. The system uses a hybrid approach combining machine learning and traditional feature analysis to provide accurate and reliable phishing detection.

Project Structure
----------------
1. Frontend (public/):
   - index.php: Main entry point and routing
   - api.php: API endpoints for external access
   - assets/: CSS, JS, and image files

2. Backend (backend/):
   - controllers/: Contains all controller classes
   - models/: Contains database models
   - views/: Contains all view templates
   - config/: Configuration files
   - ml/: Machine learning components

3. Machine Learning (backend/ml/):
   - train_model.py: Model training script
   - predict.py: Flask API for predictions
   - phishing_model.joblib: Trained model file
   - safe.csv: Dataset of legitimate URLs
   - phishing.csv: Dataset of phishing URLs

Main Components
--------------
1. Authentication System:
   - Files: 
     * backend/controllers/AuthController.php
     * backend/views/login.php
     * backend/views/register.php
   - Features: User registration, login, and session management

2. URL Scanning System (Hybrid Approach):
   - Primary Method: Machine Learning
     * Files:
       - backend/ml/predict.py (Flask API)
       - backend/ml/phishing_model.joblib (Trained model)
     * Features:
       - Real-time URL analysis
       - High accuracy predictions
       - Confidence scores
       - Feature importance analysis

   - Fallback Method: PHP-based Analysis
     * Files:
       - backend/models/URLScan.php
     * Features:
       - Traditional feature extraction
       - Risk score calculation
       - Pattern matching
       - Brand name detection

   - Integration:
     * PHP calls ML API first
     * Falls back to PHP analysis if API fails
     * Combines results for final decision

3. Admin Panel:
   - Files:
     * backend/controllers/AdminController.php
     * backend/views/admin/dashboard.php
     * backend/views/admin/users.php
     * backend/views/admin/reports.php
     * backend/views/admin/blacklist.php
   - Features:
     * User management
     * Scan reports management
     * Domain blacklist management
     * System statistics

4. User Dashboard:
   - Files:
     * backend/controllers/DashboardController.php
     * backend/views/dashboard.php
     * backend/views/report.php
   - Features:
     * Scan history
     * Personal statistics
     * Report viewing

5. API System:
   - Files:
     * public/api.php
     * backend/controllers/APIController.php
   - Features:
     * External API access
     * API key management
     * Rate limiting

Machine Learning System
---------------------
1. Model Training:
   - Algorithm: RandomForestClassifier
   - Features:
     * URL length
     * Domain length
     * Path length
     * Dots in domain
     * IP address presence
     * @ symbol presence
     * HTTPS usage
     * Multiple subdomains
     * Hexadecimal characters
     * Numbers
     * Special characters
     * Random strings

2. Training Process:
   - Data Collection:
     * safe.csv: 31MB of legitimate URLs
     * phishing.csv: 4.1MB of known phishing URLs
   - Feature Extraction:
     * Automated feature extraction from URLs
     * Normalization and preprocessing
   - Model Training:
     * 80% training, 20% testing split
     * Cross-validation
     * Hyperparameter tuning
   - Model Evaluation:
     * Accuracy metrics
     * Feature importance analysis
     * Confusion matrix

3. Model Deployment:
   - Flask API service
   - Real-time predictions
   - Error handling
   - Fallback mechanism

Database Structure
-----------------
Main tables:
1. users: User accounts and authentication
2. url_scans: Scan history and results
3. domain_blacklist: Blacklisted domains
4. api_keys: API access keys

Requirements
-----------
1. Server Requirements:
   - PHP 7.4 or higher
   - MySQL 5.7 or higher
   - Python 3.7 or higher
   - Apache/Nginx web server
   - mod_rewrite enabled
   - SSL certificate (recommended)

2. PHP Extensions:
   - PDO
   - MySQLi
   - cURL
   - JSON
   - OpenSSL

3. Python Dependencies:
   - pandas
   - numpy
   - scikit-learn
   - joblib
   - flask
   - flask-cors


Configuration
------------
1. Database Configuration:
   - Edit backend/config/Database.php
   - Set database credentials

2. ML Service Configuration:
   - Edit backend/ml/predict.py
   - Set API host and port
   - Configure error handling

3. API Configuration:
   - Set API rate limits
   - Configure API key expiration

Security Features
---------------
1. URL Analysis:
   - Machine Learning prediction
   - Domain age check
   - SSL certificate validation
   - IP address detection
   - Special character analysis
   - Brand name detection
   - Suspicious word detection

2. User Security:
   - Password hashing
   - Session management
   - CSRF protection
   - XSS prevention

3. API Security:
   - API key authentication
   - Rate limiting
   - Request validation

Usage
-----
1. User Access:
   - Register/Login at /login
   - Access dashboard at /dashboard
   - Scan URLs at /scan
   - View reports at /reports

2. Admin Access:
   - Login at /admin/login
   - Access admin panel at /admin/dashboard
   - Manage users at /admin/users
   - View all scans at /admin/reports
   - Manage blacklist at /admin/blacklist

3. API Usage:
   - Get API key from admin panel
   - Use API endpoints:
     * POST /api/scan - Scan URL
     * GET /api/history - Get scan history
     * GET /api/reports - Get reports

Maintenance
----------
1. Regular Updates:
   - Update PHP and Python dependencies
   - Check for security patches
   - Update blacklist database
   - Retrain ML model periodically

2. Backup:
   - Regular database backups
   - Configuration file backups
   - Log file management
   - ML model backups

3. Monitoring:
   - Check error logs
   - Monitor API usage
   - Track system performance
   - Monitor ML service

Troubleshooting
--------------
Common Issues:
1. Database Connection:
   - Check credentials in Database.php
   - Verify database server is running

2. URL Scanning:
   - Check internet connection
   - Verify WHOIS service availability
   - Check API rate limits
   - Verify ML service is running

3. User Access:
   - Clear browser cache
   - Check session configuration


4. ML Service:
   - Check Python service is running
   - Verify model file exists
   - Check API endpoint accessibility
   - Monitor error logs


License
-------
This project is licensed by team just graduation
if u need please contact leader of team abdishakurabdi@gmail.com
