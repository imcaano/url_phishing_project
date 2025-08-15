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
    <title>Import Domains - URL Phishing Detection</title>
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/style.css">
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .upload-area:hover, .upload-area.dragover {
            border-color: #0d6efd;
            background: #e7f1ff;
        }
        .upload-icon {
            font-size: 48px;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .upload-area.dragover .upload-icon {
            color: #0d6efd;
        }
        #file {
            display: none;
        }
        .progress {
            display: none;
            margin: 20px 0;
            height: 25px;
        }
        .progress-bar {
            font-size: 14px;
            line-height: 25px;
        }
        .file-info {
            display: none;
            margin: 20px 0;
            padding: 15px;
            border-radius: 8px;
            background: #e7f1ff;
        }
        .sample-data {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .sample-data pre {
            margin: 0;
            padding: 15px;
            background: #fff;
            border-radius: 4px;
        }
        .requirements-list {
            list-style-type: none;
            padding: 0;
        }
        .requirements-list li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        .requirements-list li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #28a745;
        }
        .alert {
            margin-bottom: 20px;
        }
        .download-sample {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>URL Scanner</h2>
            </div>
            <nav>
                <a href="/url_phishing_project/public/dashboard">
                    <i class="fas fa-home"></i> Dashboard
                </a>
                <a href="/url_phishing_project/public/predict">
                    <i class="fas fa-search"></i> Scan URL
                </a>
                <a href="/url_phishing_project/public/report">
                    <i class="fas fa-history"></i> Scan History
                </a>
                <a href="/url_phishing_project/public/blacklist">
                    <i class="fas fa-ban"></i> Domain Blacklist
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/url_phishing_project/public/admin/users">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <a href="/url_phishing_project/public/import" class="active">
                    <i class="fas fa-file-import"></i> Import Domains
                </a>
                <?php endif; ?>
                <a href="/url_phishing_project/public/logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1><i class="fas fa-file-import"></i> Import Domains</h1>
            </header>

            <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['success'] ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <i class="fas fa-<?php echo $_SESSION['success'] ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php 
                unset($_SESSION['message']);
                unset($_SESSION['success']);
            endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Upload CSV File</h5>
                            <form action="/url_phishing_project/public/import" method="POST" enctype="multipart/form-data" id="uploadForm">
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                    <h4>Drag & Drop CSV File Here</h4>
                                    <p class="text-muted">or</p>
                                    <button type="button" class="btn btn-primary" onclick="document.getElementById('file').click()">
                                        <i class="fas fa-folder-open"></i> Choose File
                                    </button>
                                    <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                                </div>

                                <div class="file-info alert alert-info" id="fileInfo">
                                    <i class="fas fa-file-alt"></i> 
                                    Selected file: <strong id="fileName">No file selected</strong>
                                </div>
                                
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: 0%">
                                        Processing...
                                    </div>
                                </div>

                                <div class="text-center">
                                    <button type="submit" class="btn btn-success btn-lg" id="importButton" style="display: none;">
                                        <i class="fas fa-file-import"></i> Import Domains
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Requirements</h5>
                            <ul class="requirements-list">
                                <li>File must be in CSV format</li>
                                <li>First row must be header row</li>
                                <li>Two columns: url and type</li>
                                <li>Type must be 'phishing' or 'legit'</li>
                                <li>URLs can be domains or full URLs</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Sample Format</h5>
                            <div class="sample-data">
                                <pre>url,type
example.com,phishing
goodsite.com,legit
malicious.net,phishing
safe.org,legit</pre>
                            </div>
                            <a href="#" class="btn btn-outline-primary btn-sm download-sample">
                                <i class="fas fa-download"></i> Download Sample CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Import History -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Recently Imported Domains</h5>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Domain</th>
                                    <th>Type</th>
                                    <th>Added By</th>
                                    <th>Date Added</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($recentImports) && !empty($recentImports)): ?>
                                    <?php foreach ($recentImports as $domain): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($domain['domain']); ?></td>
                                        <td>
                                            <span class="badge <?php echo $domain['type'] === 'phishing' ? 'bg-danger' : 'bg-success'; ?>">
                                                <?php echo ucfirst($domain['type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($domain['username']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($domain['added_at'])); ?></td>
                                        <td>
                                            <span class="badge bg-success">Imported</span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center">No recent imports</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadArea = document.getElementById('uploadArea');
            const fileInput = document.getElementById('file');
            const importButton = document.getElementById('importButton');
            const form = document.getElementById('uploadForm');
            const progressBar = document.querySelector('.progress');
            const progressBarInner = document.querySelector('.progress-bar');
            const fileInfo = document.getElementById('fileInfo');
            const fileName = document.getElementById('fileName');

            // Drag and drop functionality
            ['dragenter', 'dragover'].forEach(eventName => {
                uploadArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            }

            function unhighlight(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            }

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    handleFileSelect();
                }
            });

            // File input change
            fileInput.addEventListener('change', handleFileSelect);

            function handleFileSelect() {
                const file = fileInput.files[0];
                if (file) {
                    if (file.name.toLowerCase().endsWith('.csv')) {
                        fileName.textContent = file.name;
                        fileInfo.style.display = 'block';
                        importButton.style.display = 'inline-block';
                    } else {
                        alert('Please select a CSV file');
                        fileInput.value = '';
                        fileInfo.style.display = 'none';
                        importButton.style.display = 'none';
                    }
                }
            }

            // Form submission
            form.addEventListener('submit', function(e) {
                progressBar.style.display = 'flex';
                importButton.disabled = true;
                
                let progress = 0;
                const interval = setInterval(function() {
                    progress += 5;
                    if (progress <= 90) {
                        progressBarInner.style.width = progress + '%';
                        progressBarInner.textContent = 'Processing... ' + progress + '%';
                    }
                }, 500);

                setTimeout(function() {
                    clearInterval(interval);
                }, 10000);
            });
        });
    </script>
</body>
</html> 