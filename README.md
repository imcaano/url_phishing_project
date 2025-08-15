# URL Phishing Detection System

A comprehensive machine learning-based URL phishing detection system with web interface and admin panel.

## 🚀 Quick Start Guide

### Prerequisites
- **Python 3.8+** (for ML backend)
- **PHP 7.4+** (for web application)
- **MySQL/MariaDB** (for database)
- **XAMPP/WAMP** (for local development)
- **Git** (for version control)

## 📁 Project Structure

```
url_phishing_project/
├── backend/
│   ├── ml/                    # Machine Learning Models & Data
│   ├── controllers/           # PHP Controllers
│   ├── models/               # PHP Models
│   ├── views/                # PHP Views
│   └── config/               # Configuration Files
├── public/                   # Web Root Directory
├── database/                 # Database Scripts
├── ml_api/                   # Python Flask ML API
└── assets/                   # Static Assets
```

## 🔧 Setup Instructions

### 1. Database Setup

1. **Start XAMPP/WAMP**
   - Start Apache and MySQL services
   - Ensure MySQL is running on port 3306

2. **Create Database**
   ```sql
   CREATE DATABASE url_phishing_db;
   USE url_phishing_db;
   ```

3. **Import Database Schema**
   ```bash
   # Run the database creation scripts
   mysql -u root -p url_phishing_db < database/create_tables.sql
   ```

### 2. Python ML Backend Setup

1. **Navigate to ML API Directory**
   ```bash
   cd ml_api
   ```

2. **Create Virtual Environment**
   ```bash
   python -m venv venv
   
   # Windows
   venv\Scripts\activate
   
   # Linux/Mac
   source venv/bin/activate
   ```

3. **Install Dependencies**
   ```bash
   pip install -r requirements.txt
   ```

4. **Start ML API Server**
   ```bash
   python app.py
   ```
   
   The ML API will run on `http://localhost:5000`

### 3. Web Application Setup

1. **Configure Database Connection**
   - Edit `backend/config/Database.php`
   - Update database credentials if needed

2. **Set File Permissions**
   ```bash
   # Ensure web server can write to necessary directories
   chmod 755 public/
   chmod 755 backend/
   ```

3. **Access Web Application**
   - Open browser: `http://localhost/url_phishing_project/public/`
   - Admin panel: `http://localhost/url_phishing_project/public/admin/`

## 🚀 Running the System

### Option 1: Manual Startup

#### Start ML Backend (Terminal 1)
```bash
cd ml_api
python app.py
```

#### Start Web Server (Terminal 2)
```bash
# XAMPP/WAMP should already be running
# Access via browser: http://localhost/url_phishing_project/public/
```

### Option 2: Automated Startup Scripts

#### Windows (PowerShell)
```powershell
# Run as Administrator
.\start_system.ps1
```

#### Linux/Mac (Bash)
```bash
chmod +x start_system.sh
./start_system.sh
```

## 📋 System Requirements

### ML Backend
- **Python**: 3.8+
- **RAM**: Minimum 4GB (8GB recommended)
- **Storage**: 2GB free space
- **Dependencies**: See `ml_api/requirements.txt`

### Web Application
- **PHP**: 7.4+
- **Web Server**: Apache/Nginx
- **Database**: MySQL 5.7+ / MariaDB 10.2+
- **Extensions**: PDO, cURL, JSON

## 🔍 Testing the System

### 1. Test ML API
```bash
curl http://localhost:5000/health
# Should return: {"status": "healthy", "model": "loaded"}
```

### 2. Test Web Application
- Open: `http://localhost/url_phishing_project/public/`
- Try scanning a test URL
- Check admin panel functionality

### 3. Test Admin Features
- Login with admin credentials
- Test URL scanning
- Check blacklist management
- Verify user management

## 🛠️ Troubleshooting

### Common Issues

#### ML API Won't Start
- Check Python version: `python --version`
- Verify virtual environment is activated
- Check port 5000 is not in use
- Review error logs in terminal

#### Web Application Errors
- Verify Apache/MySQL are running
- Check database connection
- Review PHP error logs
- Ensure file permissions are correct

#### Database Connection Issues
- Verify MySQL service is running
- Check database credentials
- Ensure database exists
- Test connection manually

### Logs Location
- **ML API**: Terminal output
- **Web App**: Apache error logs
- **Database**: MySQL error logs

## 📊 Monitoring

### Health Checks
- **ML API**: `http://localhost:5000/health`
- **Web App**: Check browser console
- **Database**: MySQL status

### Performance Metrics
- Scan response time
- ML model accuracy
- Database query performance
- Memory usage

## 🔒 Security Notes

- Change default database passwords
- Use HTTPS in production
- Implement rate limiting
- Regular security updates
- Monitor access logs

## 📞 Support

For issues or questions:
1. Check this README
2. Review error logs
3. Check system requirements
4. Verify all services are running

## 🚀 Production Deployment

### Requirements
- Production web server (Nginx/Apache)
- SSL certificate
- Database server
- Load balancer (if needed)
- Monitoring tools

### Steps
1. Set production environment variables
2. Configure web server
3. Set up SSL
4. Configure database
5. Set up monitoring
6. Test thoroughly

---

**Happy Phishing Detection! 🛡️✨** 