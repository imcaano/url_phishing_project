<?php
namespace App\Models;

use App\Config\Database;
use PDO;

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getDB();
    }
    
    public function authenticate($email, $password) {
        $stmt = $this->db->prepare(
            "SELECT id, username, password, role FROM users WHERE email = ? AND status = 'active'"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            
            return [
                'success' => true,
                'user_id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];
        }
        
        return ['success' => false];
    }
    
    private function validatePassword($password) {
        if (empty($password)) {
            return ['valid' => false, 'error' => 'Password is required'];
        }
        
        if (strlen($password) < 6) {
            return ['valid' => false, 'error' => 'Password must be at least 6 characters long'];
        }
        
        return ['valid' => true];
    }
    
    public function create($userData) {
        // Validate email format
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Invalid email format'];
        }
        
        // Validate password
        $passwordValidation = $this->validatePassword($userData['password']);
        if (!$passwordValidation['valid']) {
            return ['success' => false, 'error' => $passwordValidation['error']];
        }
        
        // Check if email exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$userData['email']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Email already exists'];
        }
        
        // Check if username exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$userData['username']]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'Username already exists'];
        }
        
        // Use provided role or default to 'user'
        $role = isset($userData['role']) && in_array($userData['role'], ['user', 'admin']) ? $userData['role'] : 'user';
        
        // Create user
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)"
        );
        
        $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);
        if ($stmt->execute([$userData['username'], $userData['email'], $hashedPassword, $role])) {
            return ['success' => true, 'user_id' => $this->db->lastInsertId()];
        }
        
        return ['success' => false, 'error' => 'Registration failed'];
    }
    
    public function updateUser($userId, $data) {
        $updates = [];
        $params = [];
        
        if (isset($data['username'])) {
            $updates[] = "username = ?";
            $params[] = $data['username'];
        }
        
        if (isset($data['email'])) {
            $updates[] = "email = ?";
            $params[] = $data['email'];
        }
        
        if (isset($data['status'])) {
            $updates[] = "status = ?";
            $params[] = $data['status'];
        }
        
        if (isset($data['role'])) {
            $updates[] = "role = ?";
            $params[] = $data['role'];
        }

        if (isset($data['password'])) {
            // Validate password if it's being updated
            $passwordValidation = $this->validatePassword($data['password']);
            if (!$passwordValidation['valid']) {
                return false; // Password validation failed
            }
            
            $updates[] = "password = ?";
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
        $params[] = $userId;
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    public function getUser($userId) {
        $stmt = $this->db->prepare(
            "SELECT id, username, email, role, status, last_login FROM users WHERE id = ?"
        );
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getAllUsers() {
        $stmt = $this->db->query(
            "SELECT id, username, email, role, status, last_login FROM users ORDER BY created_at DESC"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getTotalUsers() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (\PDOException $e) {
            error_log("Error getting total users: " . $e->getMessage());
            return 0;
        }
    }
    
    private function updateLastLogin($userId) {
        $stmt = $this->db->prepare(
            "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?"
        );
        $stmt->execute([$userId]);
    }

    public function getUserById($userId) {
        try {
            $stmt = $this->db->prepare("SELECT id, username, email, role, status, created_at, last_login FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            error_log("Error getting user by ID: " . $e->getMessage());
            return null;
        }
    }

    public function verifyPassword($userId, $password) {
        try {
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && password_verify($password, $result['password'])) {
                return true;
            }
            return false;
        } catch (\PDOException $e) {
            error_log("Error verifying password: " . $e->getMessage());
            return false;
        }
    }

    public function getUserByUsernameOrEmail($usernameOrEmail) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteUser($userId) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }
} 