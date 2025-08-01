<?php

try {
    // First connect without database name to create it if it doesn't exist
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS url_phishing_db");
    echo "Database created successfully\n";

    // Connect to the newly created database
    $pdo = new PDO("mysql:host=localhost;dbname=url_phishing_db", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
        last_login DATETIME,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Users table created successfully\n";

    // Create url_scans table
    $pdo->exec("CREATE TABLE IF NOT EXISTS url_scans (
        id INT AUTO_INCREMENT PRIMARY KEY,
        url VARCHAR(2048) NOT NULL,
        user_id INT,
        is_phishing BOOLEAN NOT NULL,
        confidence_score DECIMAL(5,2) NULL,
        risk_level VARCHAR(20) DEFAULT 'UNKNOWN',
        scan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        features JSON,
        expert_analysis JSON,
        whois_info JSON,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )");
    echo "URL scans table created successfully\n";

    // Create api_keys table
    $pdo->exec("CREATE TABLE IF NOT EXISTS api_keys (
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
    )");
    echo "API keys table created successfully\n";

    // Create domain_blacklist table
    $pdo->exec("CREATE TABLE IF NOT EXISTS domain_blacklist (
        id INT AUTO_INCREMENT PRIMARY KEY,
        domain VARCHAR(255) NOT NULL UNIQUE,
        reason VARCHAR(255) NOT NULL,
        added_by INT NOT NULL,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (added_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_domain (domain),
        INDEX idx_added_by (added_by),
        INDEX idx_added_at (added_at)
    )");
    echo "Domain blacklist table created successfully\n";

    // Create domain_reports table
    $pdo->exec("CREATE TABLE IF NOT EXISTS domain_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        domain VARCHAR(255) NOT NULL,
        reported_by INT NOT NULL,
        reason VARCHAR(50) NOT NULL,
        report_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE,
        INDEX idx_domain (domain)
    )");
    echo "Domain reports table created successfully\n";

    // Create scanned_domains table to track all scanned domains
    $pdo->exec("CREATE TABLE IF NOT EXISTS scanned_domains (
        id INT AUTO_INCREMENT PRIMARY KEY,
        domain VARCHAR(255) NOT NULL,
        first_scan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        last_scan_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_scans INT DEFAULT 1,
        phishing_count INT DEFAULT 0,
        safe_count INT DEFAULT 0,
        average_confidence_score DECIMAL(5,2) DEFAULT 0.00,
        last_scan_result JSON,
        whois_info JSON,
        domain_age VARCHAR(100) DEFAULT 'Unknown',
        domain_registrar VARCHAR(255) DEFAULT 'Unknown',
        domain_status VARCHAR(100) DEFAULT 'Unknown',
        is_blacklisted BOOLEAN DEFAULT FALSE,
        blacklist_date TIMESTAMP NULL,
        blacklist_reason VARCHAR(255) NULL,
        INDEX idx_domain (domain),
        INDEX idx_last_scan_date (last_scan_date),
        INDEX idx_is_blacklisted (is_blacklisted)
    )");
    echo "Scanned domains table created successfully\n";

    // Create indexes
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_url_scans_user ON url_scans(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_url_scans_date ON url_scans(scan_date)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_api_keys_user ON api_keys(user_id)");
    $pdo->exec("CREATE INDEX IF NOT EXISTS idx_domain_reports_date ON domain_reports(report_date)");
    echo "Indexes created successfully\n";

    // Create an admin user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@example.com' LIMIT 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', 'admin@example.com', $password, 'admin']);
        echo "Admin user created successfully\n";
    }

    echo "Database setup completed successfully!\n";
    echo "You can now log in with:\n";
    echo "Email: admin@example.com\n";
    echo "Password: admin123\n";

} catch(PDOException $e) {
    die("Error: " . $e->getMessage() . "\n");
} 