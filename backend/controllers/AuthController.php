<?php
namespace App\Controllers;

use App\Models\User;

class AuthController {
    private $user;
    
    public function __construct() {
        $this->user = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            try {
                $result = $this->user->authenticate($email, $password);
                
                if ($result && $result['success']) {
                    $_SESSION['user_id'] = $result['user_id'];
                    $_SESSION['username'] = $result['username'];
                    $_SESSION['role'] = $result['role'];
                    
                    // Redirect based on role
                    if ($result['role'] === 'admin') {
                        header('Location: /url_phishing_project/public/admin/dashboard');
                    } else {
                        header('Location: /url_phishing_project/public/dashboard');
                    }
                    exit;
                } else {
                    $_SESSION['error'] = "Invalid email or password";
                    header('Location: /url_phishing_project/public/login');
                    exit;
                }
            } catch (\Exception $e) {
                error_log("Login Error: " . $e->getMessage());
                $_SESSION['error'] = "An error occurred during login";
                header('Location: /url_phishing_project/public/login');
                exit;
            }
        }
        
        include __DIR__ . '/../views/login.php';
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $result = $this->user->create([
                'username' => $username,
                'email' => $email,
                'password' => $password
            ]);
            
            if ($result['success']) {
                header('Location: /url_phishing_project/public/login');
                exit;
            } else {
                $error = $result['error'];
            }
        }
        
        include __DIR__ . '/../views/register.php';
    }

    public function checkAdminAccess() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /url_phishing_project/public/login');
            exit;
        }
    }

    public function logout() {
        session_destroy();
        header('Location: /url_phishing_project/public/login');
        exit;
    }
} 