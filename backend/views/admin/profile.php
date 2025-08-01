<?php
// Check if user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /url_phishing_project/public/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - URL Phishing Detection</title>
    <link rel="icon" type="image/png" href="/url_phishing_project/public/assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
            --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            background: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            padding: 1.5rem;
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            padding: 1rem 0;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 1.5rem;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            margin: 0;
            color: white;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.8rem 1rem;
            margin: 0.2rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
        }

        .nav-link i {
            width: 25px;
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .main-content {
            margin-left: 250px;
            padding: 2rem;
            min-height: 100vh;
        }

        .top-navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .profile-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .profile-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 2rem;
            background: var(--light-color);
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--primary-color);
        }

        .profile-info h3 {
            margin: 0 0 1rem 0;
            color: var(--dark-color);
            font-size: 1.75rem;
            font-weight: 700;
        }

        .profile-info p {
            margin: 0.75rem 0;
            color: var(--secondary-color);
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .profile-info i {
            color: var(--primary-color);
            width: 20px;
        }

        .role-badge {
            display: inline-block;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .form-section {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .form-section h4 {
            color: var(--dark-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .form-section h4 i {
            color: var(--primary-color);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
        }

        .form-control:disabled {
            background: #f8f9fa;
            color: var(--secondary-color);
            cursor: not-allowed;
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: #224abe;
        }

        .btn-secondary {
            background: var(--warning-color);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: background 0.3s ease;
        }

        .btn-secondary:hover {
            background: #e0a800;
        }

        .alert {
            margin-bottom: 1.5rem;
        }

        .password-section {
            background: var(--light-color);
            padding: 2rem;
            border-radius: 10px;
            border-left: 4px solid var(--warning-color);
            margin-top: 2rem;
        }

        .password-section h4 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .password-note {
            background: rgba(246, 194, 62, 0.1);
            padding: 1rem;
            border-radius: 8px;
            border-left: 3px solid var(--warning-color);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--dark-color);
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
            
            .profile-header {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
            }
            
            .profile-avatar {
                width: 100px;
                height: 100px;
            }
        }

        .input-group {
            position: relative;
        }

        .input-group .form-control {
            padding-right: 3rem;
        }

        .input-group .toggle-password {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            transition: var(--transition);
        }

        .input-group .toggle-password:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h1><i class="fas fa-shield-alt me-2"></i>Admin Panel</h1>
        </div>
        <nav>
            <a href="/url_phishing_project/public/admin/dashboard" class="nav-link">
                <i class="fas fa-home"></i> Dashboard
            </a>
            <a href="/url_phishing_project/public/admin/scan" class="nav-link">
                <i class="fas fa-search"></i> Scan URL
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
                <i class="fas fa-file-alt"></i> Scan Reports
            </a>
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/import" class="nav-link">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <a href="/url_phishing_project/public/admin/profile" class="nav-link active">
                <i class="fas fa-user"></i> Profile
            </a>
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h2>Admin Profile</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="profile-container">
            <div class="profile-section">
                <?php if (isset($message)): ?>
                <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                    <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <h2>Profile Information</h2>

                <div class="profile-header">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['username']); ?>&background=4e73df&color=fff&size=120" alt="Profile Avatar" class="profile-avatar">
                    <div class="profile-info">
                        <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                        <p><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><i class="fas fa-calendar-alt"></i> Member since <?php echo date('F Y', strtotime($user['created_at'] ?? 'now')); ?></p>
                        <div class="role-badge">
                            <i class="fas fa-crown me-1"></i> Administrator
                        </div>
                    </div>
                </div>

                <form action="/url_phishing_project/public/admin/profile/update" method="post" id="profileForm">
                    <div class="form-section">
                        <h4>Personal Information</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="password-section">
                        <h4>Change Password</h4>
                        <div class="password-note">
                            <i class="fas fa-info-circle me-2"></i>
                            Leave password fields empty if you don't want to change your password.
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="current_password">Current Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password">
                                        <button type="button" class="toggle-password" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="new_password">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password">
                                        <button type="button" class="toggle-password" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                        <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const button = input.nextElementSibling;
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye';
            }
        }

        // Reset form to original values
        function resetForm() {
            if (confirm('Are you sure you want to reset the form? All changes will be lost.')) {
                document.getElementById('profileForm').reset();
            }
        }

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword || confirmPassword) {
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('New password and confirm password do not match!');
                    return false;
                }
                
                if (newPassword.length > 0 && newPassword.length < 8) {
                    e.preventDefault();
                    alert('New password must be at least 8 characters long!');
                    return false;
                }
            }
        });

        // Add smooth animations
        document.addEventListener('DOMContentLoaded', function() {
            const profileSection = document.querySelector('.profile-section');
            profileSection.style.opacity = '0';
            profileSection.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                profileSection.style.transition = 'all 0.6s ease';
                profileSection.style.opacity = '1';
                profileSection.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html> 