<?php
namespace App\Config;

class Database {
    private static $instance = null;
    private $host = "localhost";
    private $db_name = "url_phishing_db";
    private $username = "root";
    private $password = "";
    private $conn;

    private function __construct() {}

    public static function getDB() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance->getConnection();
    }

    public function getConnection() {
        if ($this->conn === null) {
            try {
                $this->conn = new \PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $this->conn->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
            } catch(\PDOException $e) {
                error_log("Database connection error: " . $e->getMessage());
                throw new \Exception("Could not connect to the database. Please check your configuration.");
            }
        }
        return $this->conn;
    }
} 