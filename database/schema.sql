-- Create database if not exists
CREATE DATABASE IF NOT EXISTS url_phishing_db;
USE url_phishing_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- URL scans table
CREATE TABLE IF NOT EXISTS url_scans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    url VARCHAR(2048) NOT NULL,
    user_id INT,
    is_phishing BOOLEAN NOT NULL,
    confidence_score DECIMAL(5,2) NOT NULL,
    scan_features JSON,
    scan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- API keys table
CREATE TABLE IF NOT EXISTS api_keys (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    api_key VARCHAR(64) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    daily_limit INT DEFAULT 100,
    requests_today INT DEFAULT 0,
    last_reset_date DATE,
    last_used DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Domain blacklist table
CREATE TABLE IF NOT EXISTS domain_blacklist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) NOT NULL UNIQUE,
    reason VARCHAR(255) NOT NULL,
    added_by INT NOT NULL,
    status ENUM('reported', 'confirmed') DEFAULT 'reported',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_domain (domain),
    INDEX idx_status (status)
);

-- Domain reports table (for tracking user reports of suspicious domains)
CREATE TABLE IF NOT EXISTS domain_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain VARCHAR(255) NOT NULL,
    reported_by INT NOT NULL,
    reason VARCHAR(50) NOT NULL,
    report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_domain (domain)
);

-- Create indexes
CREATE INDEX idx_url_scans_user ON url_scans(user_id);
CREATE INDEX idx_url_scans_date ON url_scans(scan_date);
CREATE INDEX idx_api_keys_user ON api_keys(user_id);
CREATE INDEX idx_domain_reports_date ON domain_reports(report_date); 