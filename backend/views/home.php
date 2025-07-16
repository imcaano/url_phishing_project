<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL Phishing Detection - Welcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --background-color: #f8f9fc;
            --glass-bg: rgba(255, 255, 255, 0.9);
        }

        body {
            background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }

        .animated-background {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            background: linear-gradient(45deg, #4e73df55, #1cc88a55);
            animation: gradientBG 15s ease infinite;
            background-size: 400% 400%;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .shape {
            position: fixed;
            background: linear-gradient(135deg, var(--primary-color), var(--success-color));
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            opacity: 0.15;
            filter: blur(5px);
            z-index: 0;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 300px;
            height: 300px;
            bottom: -150px;
            left: -150px;
            animation-delay: 2s;
        }

        .shape-3 {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 15%;
            animation-delay: 4s;
        }

        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
            100% { transform: translateY(0) rotate(360deg); }
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        .auth-container {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            width: 100%;
            max-width: 1000px;
            display: flex;
            overflow: hidden;
            animation: slideUp 0.5s ease-out;
        }

        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .auth-info {
            flex: 1;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .auth-info::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(78, 115, 223, 0.1), rgba(28, 200, 138, 0.1));
            z-index: 0;
        }

        .auth-info h1 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            font-size: 2.5em;
            font-weight: 700;
            position: relative;
            z-index: 1;
        }

        .auth-info p {
            color: #4a5568;
            margin-bottom: 2rem;
            line-height: 1.8;
            font-size: 1.1rem;
            position: relative;
            z-index: 1;
        }

        .auth-forms {
            flex: 1;
            padding: 3rem;
            background: var(--glass-bg);
        }

        .tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 2px solid rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .tab {
            padding: 1rem 2rem;
            cursor: pointer;
            color: #718096;
            font-weight: 600;
            position: relative;
            transition: all 0.3s ease;
        }

        .tab.active {
            color: var(--primary-color);
        }

        .tab.active:after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary-color);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { transform: scaleX(0); }
            to { transform: scaleX(1); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #4a5568;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.25);
            background: white;
            outline: none;
        }

        .btn-primary {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), #2e59d9);
            color: white;
            border: none;
            border-radius: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(78, 115, 223, 0.4);
        }

        .alert {
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #registerForm {
            display: none;
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="animated-background"></div>
    <div class="shape shape-1"></div>
    <div class="shape shape-2"></div>
    <div class="shape shape-3"></div>

    <div class="container">
        <div class="auth-container">
            <div class="auth-info">
                <i class="fas fa-shield-alt feature-icon"></i>
                <h1>URL Phishing Detection System</h1>
                <p>Protect yourself and your organization from phishing attacks with our advanced URL detection system. Simply log in or create an account to start scanning suspicious URLs.</p>
            </div>
            
            <div class="auth-forms">
                <div class="tabs">
                    <div class="tab active" onclick="showForm('login')">Login</div>
                    <div class="tab" onclick="showForm('register')">Register</div>
                </div>
                
                <form id="loginForm" method="post" action="/url_phishing_project/public/login">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope me-2"></i>Email Address</label>
                        <input type="email" name="email" required placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock me-2"></i>Password</label>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
                
                <form id="registerForm" method="post" action="/url_phishing_project/public/register">
                    <div class="form-group">
                        <label><i class="fas fa-user me-2"></i>Username</label>
                        <input type="text" name="username" required placeholder="Choose a username">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-envelope me-2"></i>Email Address</label>
                        <input type="email" name="email" required placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-lock me-2"></i>Password</label>
                        <input type="password" name="password" required placeholder="Create a password" minlength="6">
                    </div>
                    
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    function showForm(formType) {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const tabs = document.querySelectorAll('.tab');
        
        if (formType === 'login') {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            tabs[0].classList.add('active');
            tabs[1].classList.remove('active');
        } else {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            tabs[0].classList.remove('active');
            tabs[1].classList.add('active');
        }
    }

    // Add floating animation to shapes
    document.querySelectorAll('.shape').forEach(shape => {
        shape.style.transform = `translate(${Math.random() * 20 - 10}px, ${Math.random() * 20 - 10}px)`;
    });
    </script>
</body>
</html> 