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
    <title>Import Domains - Admin</title>
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
        }

        body {
            background: var(--light-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .admin-section {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .admin-section h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }

        .upload-area {
            border: 2px dashed var(--primary-color);
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
            background: var(--light-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            background: rgba(78, 115, 223, 0.1);
        }

        .upload-area i {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .upload-area p {
            margin: 0;
            color: var(--secondary-color);
        }

        .progress {
            height: 25px;
            margin: 1rem 0;
            display: none;
        }

        .requirements-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .requirements-list li {
            padding: 0.5rem 0;
            color: var(--secondary-color);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .requirements-list li i {
            color: var(--success-color);
        }

        .sample-format {
            background: var(--light-color);
            padding: 1rem;
            border-radius: 5px;
            margin-top: 1rem;
            font-family: monospace;
            white-space: pre-wrap;
        }

        .recent-imports {
            margin-top: 2rem;
        }

        .recent-imports table {
            width: 100%;
            border-collapse: collapse;
        }

        .recent-imports th,
        .recent-imports td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .recent-imports th {
            background: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .recent-imports tr:hover {
            background: var(--light-color);
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
            <a href="/url_phishing_project/public/admin/import" class="nav-link active">
                <i class="fas fa-file-import"></i> Import Domains
            </a>
            <a href="/url_phishing_project/public/admin/profile" class="nav-link">
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
            <h2>Import Domains</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <?php if (isset($message)): ?>
            <div class="alert alert-<?php echo $success ? 'success' : 'danger'; ?>" role="alert">
                <?php echo $message; ?>
            </div>
            <?php endif; ?>

            <div class="upload-area" id="dropZone">
                <i class="fas fa-cloud-upload-alt"></i>
                <h3>Drag & Drop CSV File Here</h3>
                <p>or</p>
                <form action="/url_phishing_project/public/admin/import" method="post" enctype="multipart/form-data" id="uploadForm">
                    <input type="file" name="file" id="fileInput" accept=".csv" style="display: none;">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('fileInput').click()">
                        Choose File
                    </button>
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%"></div>
                    </div>
                </form>
            </div>

            <div class="requirements">
                <h3>CSV File Requirements</h3>
                <ul class="requirements-list">
                    <li><i class="fas fa-check"></i> File must be in CSV format</li>
                    <li><i class="fas fa-check"></i> First row should be header row</li>
                    <li><i class="fas fa-check"></i> Required columns: domain, type</li>
                    <li><i class="fas fa-check"></i> Type should be either 'phishing' or 'safe'</li>
                </ul>
                <div class="sample-format">
domain,type
example.com,phishing
legitimate.com,safe
                </div>
            </div>

            <div class="recent-imports">
                <h3>Recently Imported Domains</h3>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Domain</th>
                                <th>Type</th>
                                <th>Added By</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentImports)): ?>
                                <?php foreach ($recentImports as $import): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($import['domain']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $import['type'] === 'phishing' ? 'bg-danger' : 'bg-success'; ?>">
                                            <?php echo ucfirst($import['type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($import['username']); ?></td>
                                    <td><?php echo date('Y-m-d H:i', strtotime($import['added_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center">No recent imports found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File upload handling
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const uploadForm = document.getElementById('uploadForm');
        const progressBar = document.querySelector('.progress');
        const progressBarInner = document.querySelector('.progress-bar');

        // Handle file selection
        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                uploadForm.submit();
            }
        });

        // Handle drag and drop
        dropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.background = 'rgba(78, 115, 223, 0.1)';
        });

        dropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.background = 'var(--light-color)';
        });

        dropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.background = 'var(--light-color)';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                uploadForm.submit();
            }
        });

        // Show progress bar when form is submitted
        uploadForm.addEventListener('submit', function() {
            progressBar.style.display = 'block';
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBarInner.style.width = progress + '%';
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 500);
        });
    </script>
</body>
</html> 