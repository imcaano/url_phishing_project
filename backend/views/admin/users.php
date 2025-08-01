<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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
        }

        body {
            background: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
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
            z-index: 1000;
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
            font-weight: 600;
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
            font-weight: 500;
        }

        .nav-link:hover {
            color: white;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .top-navbar h2 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info span {
            font-weight: 500;
            color: var(--dark-color);
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }

        .admin-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: var(--shadow-light);
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 1.5rem;
        }

        /* Expert Table Design */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow-medium);
            border: 1px solid var(--border-color);
        }

        .users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            margin: 0;
        }

        .users-table th {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1rem;
            text-align: left;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: none;
        }

        .users-table th:first-child {
            border-top-left-radius: 15px;
        }

        .users-table th:last-child {
            border-top-right-radius: 15px;
        }

        .users-table td {
            padding: 1.25rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
            font-size: 0.95rem;
        }

        .users-table tr {
            transition: all 0.3s ease;
        }

        .users-table tr:hover {
            background: linear-gradient(135deg, #f8f9fc 0%, #e8f2ff 100%);
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .users-table tr:last-child td {
            border-bottom: none;
        }

        /* Consistent Button Design */
        .btn-edit {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #224abe 0%, #1a3a8f 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-delete {
            padding: 0.6rem 1.2rem;
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .btn-add {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #17a673 0%, #13855c 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        /* Enhanced Badge Design */
        .badge {
            padding: 0.6rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .badge-admin { 
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%); 
            color: white; 
        }
        
        .badge-user { 
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%); 
            color: white; 
        }
        
        .badge-active { 
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%); 
            color: white; 
        }
        
        .badge-suspended { 
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%); 
            color: white; 
        }
        
        .badge-inactive { 
            background: linear-gradient(135deg, var(--secondary-color) 0%, #6c757d 100%); 
            color: white; 
        }

        /* Alert Styling */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            box-shadow: var(--shadow-light);
        }

        .alert-success {
            background: linear-gradient(135deg, var(--success-color) 0%, #17a673 100%);
            color: white;
        }

        .alert-danger {
            background: linear-gradient(135deg, var(--danger-color) 0%, #c82333 100%);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: var(--shadow-medium);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: invert(1);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        /* Responsive Design */
        @media (max-width: 991px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
                padding: 1rem;
                margin-bottom: 1rem;
            }
            .main-content {
                margin-left: 0;
                padding: 1rem;
            }
        }

        @media (max-width: 767px) {
            .top-navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
                padding: 1rem;
            }
            .user-info {
                flex-direction: row;
                gap: 0.5rem;
            }
            .admin-section {
                padding: 1rem;
            }
            .users-table {
                font-size: 0.85rem;
            }
            .users-table th,
            .users-table td {
                padding: 0.75rem 0.5rem;
            }
            .btn-edit, .btn-delete {
                padding: 0.5rem 1rem;
                font-size: 0.8rem;
            }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Action Buttons Container */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            align-items: center;
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
            <a href="/url_phishing_project/public/admin/blacklist" class="nav-link">
                <i class="fas fa-ban"></i> Domain Blacklist
            </a>
            <a href="/url_phishing_project/public/admin/users" class="nav-link active">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <a href="/url_phishing_project/public/admin/reports" class="nav-link">
                <i class="fas fa-file-alt"></i> Scan Reports
            </a>
            <a href="/url_phishing_project/public/admin/import" class="nav-link">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <a href="/url_phishing_project/public/logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h2><i class="fas fa-users me-2"></i>User Management System</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <!-- Add User Button and Modal -->
            <button type="button" class="btn-add" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus"></i> Add New User
            </button>

            <!-- Add User Modal -->
            <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="/url_phishing_project/public/admin/users" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addUserModalLabel">
                                    <i class="fas fa-user-plus me-2"></i>Add New User
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user me-1"></i>Username
                                    </label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-envelope me-1"></i>Email
                                    </label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-lock me-1"></i>Password
                                    </label>
                                    <input type="password" name="password" id="password" class="form-control" 
                                           required minlength="6" pattern=".{6,}" 
                                           title="Password must be at least 6 characters long">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Password must be at least 6 characters long
                                    </div>
                                    <div id="password-strength" class="mt-2"></div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-user-tag me-1"></i>Role
                                    </label>
                                    <select name="role" class="form-select" required>
                                        <option value="user">User</option>
                                        <option value="admin">Admin</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-user-plus me-1"></i>Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                <i class="fas fa-<?php echo $success ? 'check-circle' : 'exclamation-triangle'; ?> me-2"></i>
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="table-container">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user me-2"></i>Username</th>
                            <th><i class="fas fa-envelope me-2"></i>Email</th>
                            <th><i class="fas fa-user-tag me-2"></i>Role</th>
                            <th><i class="fas fa-info-circle me-2"></i>Status</th>
                            <th><i class="fas fa-clock me-2"></i>Last Login</th>
                            <th><i class="fas fa-cogs me-2"></i>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                </td>
                                <td>
                                    <i class="fas fa-envelope me-1"></i>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                        <i class="fas fa-<?php echo $user['role'] === 'admin' ? 'crown' : 'user'; ?> me-1"></i>
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php 
                                        echo $user['status'] === 'active' ? 'badge-active' : 
                                            ($user['status'] === 'suspended' ? 'badge-suspended' : 'badge-inactive'); 
                                    ?>">
                                        <i class="fas fa-<?php 
                                            echo $user['status'] === 'active' ? 'check-circle' : 
                                                ($user['status'] === 'suspended' ? 'pause-circle' : 'times-circle'); 
                                        ?> me-1"></i>
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?php echo $user['last_login'] ? date('M d, Y H:i', strtotime($user['last_login'])) : 'Never'; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-edit" data-bs-toggle="modal" data-bs-target="#editUser<?php echo $user['id']; ?>">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <form action="/url_phishing_project/public/admin/users" method="POST" style="display:inline-block;">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                            <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUser<?php echo $user['id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">
                                                <i class="fas fa-edit me-2"></i>Edit User: <?php echo htmlspecialchars($user['username']); ?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="/url_phishing_project/public/admin/users" method="POST">
                                            <div class="modal-body">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-user-tag me-1"></i>Role
                                                    </label>
                                                    <select name="role" class="form-select">
                                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">
                                                        <i class="fas fa-info-circle me-1"></i>Status
                                                    </label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" <?php echo $user['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                                        <option value="suspended" <?php echo $user['status'] === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                                                        <option value="inactive" <?php echo $user['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fas fa-times me-1"></i>Cancel
                                                </button>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fas fa-save me-1"></i>Save Changes
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="fas fa-users"></i>
                                        <h4>No Users Found</h4>
                                        <p>No users have been registered yet. Add your first user using the button above.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Password validation
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        const passwordStrength = document.getElementById('password-strength');
        const addUserForm = document.querySelector('#addUserModal form');
        
        if (passwordField) {
            passwordField.addEventListener('input', function() {
                const password = this.value;
                let strength = '';
                let strengthClass = '';
                
                if (password.length === 0) {
                    strength = '<i class="fas fa-times-circle text-danger"></i> Password is required';
                    strengthClass = 'text-danger';
                } else if (password.length < 6) {
                    strength = '<i class="fas fa-exclamation-triangle text-warning"></i> Password must be at least 6 characters';
                    strengthClass = 'text-warning';
                } else {
                    strength = '<i class="fas fa-check-circle text-success"></i> Password meets requirements';
                    strengthClass = 'text-success';
                }
                
                if (passwordStrength) {
                    passwordStrength.innerHTML = strength;
                    passwordStrength.className = strengthClass;
                }
            });
        }
        
        // Form validation
        if (addUserForm) {
            addUserForm.addEventListener('submit', function(e) {
                const password = passwordField.value;
                const username = document.querySelector('input[name="username"]').value;
                const email = document.querySelector('input[name="email"]').value;
                
                // Clear previous errors
                clearErrors();
                
                let hasError = false;
                
                // Validate username
                if (!username.trim()) {
                    showError('username', 'Username is required');
                    hasError = true;
                }
                
                // Validate email
                if (!email.trim()) {
                    showError('email', 'Email is required');
                    hasError = true;
                } else if (!isValidEmail(email)) {
                    showError('email', 'Please enter a valid email address');
                    hasError = true;
                }
                
                // Validate password
                if (!password) {
                    showError('password', 'Password is required');
                    hasError = true;
                } else if (password.length < 6) {
                    showError('password', 'Password must be at least 6 characters long');
                    hasError = true;
                }
                
                if (hasError) {
                    e.preventDefault();
                    return false;
                }
            });
        }
        
        function showError(fieldName, message) {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (field) {
                field.classList.add('is-invalid');
                
                // Remove existing error message
                const existingError = field.parentNode.querySelector('.invalid-feedback');
                if (existingError) {
                    existingError.remove();
                }
                
                // Add new error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'invalid-feedback';
                errorDiv.textContent = message;
                field.parentNode.appendChild(errorDiv);
            }
        }
        
        function clearErrors() {
            const invalidFields = document.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => {
                field.classList.remove('is-invalid');
            });
            
            const errorMessages = document.querySelectorAll('.invalid-feedback');
            errorMessages.forEach(message => {
                message.remove();
            });
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    });
    </script>
</body>
</html> 