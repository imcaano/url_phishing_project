<?php
require_once __DIR__ . '/../config/database.php';

// Create database connection
$mysqli = new mysqli(
    $config['host'],
    $config['user'],
    $config['password']
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Read and execute SQL schema
$sql = file_get_contents(__DIR__ . '/../database/schema.sql');
if ($mysqli->multi_query($sql)) {
    echo "Database setup completed successfully!\n";
} else {
    echo "Error setting up database: " . $mysqli->error . "\n";
}

// Create default admin user
$adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
$mysqli->select_db('url_phishing_db');
$stmt = $mysqli->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
$username = 'admin';
$email = 'admin@example.com';
$stmt->bind_param('sss', $username, $email, $adminPassword);
$stmt->execute();

echo "Default admin user created!\n";
echo "Username: admin\n";
echo "Password: admin123\n"; 