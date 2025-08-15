<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - URL Phishing Detection System</title>
    <link rel="icon" type="image/png" href="/url_phishing_project/public/assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --danger-color: #e74a3b;
            --warning-color: #f6c23e;
            --info-color: #36b9cc;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
            --border-color: #e3e6f0;
            --shadow-light: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            --shadow-medium: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.25);
            --gradient-primary: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            --gradient-success: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%);
            --gradient-danger: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.1)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
            z-index: 1;
        }

        .register-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 3rem 2.5rem;
            box-shadow: var(--shadow-medium);
            border: 1px solid rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .register-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-success);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .register-logo {
            width: 80px;
            height: 80px;
            background: var(--gradient-success);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.3);
        }

        .register-logo i {
            font-size: 2rem;
            color: white;
        }

        .register-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .register-subtitle {
            color: var(--secondary-color);
            font-size: 1rem;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(28, 200, 138, 0.1);
            background: white;
        }

        .form-group .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-color);
            font-size: 1.1rem;
            transition: color 0.3s ease;
        }

        .form-group input:focus + .input-icon {
            color: var(--success-color);
        }

        .register-btn {
            width: 100%;
            padding: 1rem;
            background: var(--gradient-success);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            position: relative;
            overflow: hidden;
        }

        .register-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .register-btn:hover::before {
            left: 100%;
        }

        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(28, 200, 138, 0.3);
        }

        .register-btn:active {
            transform: translateY(0);
        }

        .auth-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .auth-links a {
            color: var(--success-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .auth-links a:hover {
            color: #17a673;
            text-decoration: underline;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        /* Floating Animation */
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }

        .register-card {
            animation: float 6s ease-in-out infinite;
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .register-card {
                padding: 2rem 1.5rem;
                margin: 1rem;
            }
            
            .register-title {
                font-size: 1.5rem;
            }
            
            .register-logo {
                width: 60px;
                height: 60px;
            }
            
            .register-logo i {
                font-size: 1.5rem;
            }
        }

        /* Loading State */
        .register-btn.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .register-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-left: -10px;
            margin-top: -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Password strength indicator */
        .password-strength {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: var(--secondary-color);
        }

        .strength-bar {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 0.25rem;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: var(--danger-color); width: 25%; }
        .strength-fair { background: var(--warning-color); width: 50%; }
        .strength-good { background: var(--info-color); width: 75%; }
        .strength-strong { background: var(--success-color); width: 100%; }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <div class="register-logo">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1 class="register-title">Create Account</h1>
                <p class="register-subtitle">Join our URL Phishing Detection community</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
            
            <form method="post" action="/url_phishing_project/public/register" id="registerForm">
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input type="text" id="username" name="username" required 
                           placeholder="Choose a unique username"
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    <i class="fas fa-user input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope me-2"></i>Email Address
                    </label>
                    <input type="email" id="email" name="email" required 
                           placeholder="Enter your email address"
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    <i class="fas fa-envelope input-icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input type="password" id="password" name="password" required minlength="6"
                           placeholder="Create a strong password">
                    <i class="fas fa-lock input-icon"></i>
                    <div class="password-strength">
                        <span id="strength-text">Password strength</span>
                        <div class="strength-bar">
                            <div class="strength-fill" id="strength-fill"></div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="register-btn" id="registerBtn">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? 
                    <a href="/url_phishing_project/public/login">
                        <i class="fas fa-sign-in-alt me-1"></i>Sign In
                    </a>
                </p>
                <p style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--secondary-color);">
                    <i class="fas fa-shield-alt me-1"></i>Secure URL Phishing Detection System
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form submission with loading state
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('registerBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Account...';
        });

        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthText = document.getElementById('strength-text');
            const strengthFill = document.getElementById('strength-fill');
            
            let strength = 0;
            let text = 'Very Weak';
            let className = 'strength-weak';
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    text = 'Very Weak';
                    className = 'strength-weak';
                    break;
                case 2:
                    text = 'Weak';
                    className = 'strength-weak';
                    break;
                case 3:
                    text = 'Fair';
                    className = 'strength-fair';
                    break;
                case 4:
                    text = 'Good';
                    className = 'strength-good';
                    break;
                case 5:
                case 6:
                    text = 'Strong';
                    className = 'strength-strong';
                    break;
            }
            
            strengthText.textContent = text;
            strengthFill.className = 'strength-fill ' + className;
        });

        // Input focus effects
        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    </script>
</body>
</html> 