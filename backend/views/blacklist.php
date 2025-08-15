<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Domain Blacklist - URL Phishing Detection</title>
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/style.css">
    <link rel="stylesheet" href="/url_phishing_project/public/assets/css/dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .back-button {
            margin-bottom: 20px;
        }
        .safe-web-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .safe-web-section h3 {
            margin-bottom: 15px;
        }
        .safe-web-btn {
            background-color: white;
            color: #28a745;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .safe-web-btn:hover {
            background-color: #f8f9fa;
            color: #28a745;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stats-cards {
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #007bff;
        }
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
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
                <a href="/url_phishing_project/public/blacklist" class="active">
                    <i class="fas fa-ban"></i> Domain Blacklist
                </a>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <a href="/url_phishing_project/public/admin/users">
                    <i class="fas fa-users"></i> Manage Users
                </a>
                <?php endif; ?>
                <a href="/url_phishing_project/public/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <header>
                <h1>Domain Blacklist</h1>
            </header>

            <!-- Back Button -->
            <div class="back-button">
                <a href="/url_phishing_project/public/dashboard" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Safe Web Section -->
            <div class="safe-web-section">
                <h3><i class="fas fa-shield-alt"></i> Keep the Web Safe</h3>
                <p>Help protect others by reporting malicious domains and contributing to our community-driven blacklist.</p>
                <a href="/url_phishing_project/public/predict" class="safe-web-btn">
                    <i class="fas fa-search"></i> Scan URL for Safety
                </a>
            </div>

            <!-- Statistics Cards -->
            <div class="row stats-cards">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($blacklistedDomains); ?></div>
                        <div class="stat-label">Total Blacklisted</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($blacklistedDomains, function($d) { return $d['report_count'] >= 10; })); ?></div>
                        <div class="stat-label">Confirmed Malicious</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($blacklistedDomains, function($d) { return $d['reason'] === 'phishing'; })); ?></div>
                        <div class="stat-label">Phishing Attempts</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count(array_filter($blacklistedDomains, function($d) { return $d['reason'] === 'malware'; })); ?></div>
                        <div class="stat-label">Malware Sources</div>
                    </div>
                </div>
            </div>

            <!-- Display Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo htmlspecialchars($_SESSION['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Add Domain Form - Only for Admins -->
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-plus-circle"></i> Add Domain to Blacklist</h5>
                    <form action="/url_phishing_project/public/blacklist" method="post" class="row g-3">
                        <input type="hidden" name="action" value="user_add">
                        <div class="col-md-6">
                            <label class="form-label">Domain Name</label>
                            <input type="text" class="form-control" name="domain" required 
                                   placeholder="example.com">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Reason</label>
                            <select class="form-select" name="reason" required>
                                <option value="">Select reason...</option>
                                <option value="phishing">Phishing Attempt</option>
                                <option value="malware">Malware Distribution</option>
                                <option value="scam">Scam Website</option>
                                <option value="spam">Spam Source</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Note:</strong> Only administrators can add domains to the blacklist. Regular users can view the blacklisted domains below.
            </div>
            <?php endif; ?>

            <!-- Blacklisted Domains Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Blacklisted Domains</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="fas fa-globe"></i> Domain</th>
                                    <th><i class="fas fa-exclamation-triangle"></i> Reason</th>
                                    <th><i class="fas fa-robot"></i> ML Confidence</th>
                                    <th><i class="fas fa-flag"></i> Reports Count</th>
                                    <th><i class="fas fa-info-circle"></i> Status</th>
                                    <th><i class="fas fa-calendar"></i> Added Date</th>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                    <th><i class="fas fa-cogs"></i> Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($blacklistedDomains)): ?>
                                <tr>
                                    <td colspan="<?php echo isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ? '7' : '6'; ?>" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                        No blacklisted domains found. Great job keeping the web safe!
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($blacklistedDomains as $domain): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($domain['domain']); ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                echo $domain['reason'] === 'phishing' ? 'danger' : 
                                                    ($domain['reason'] === 'malware' ? 'warning' : 
                                                    ($domain['reason'] === 'scam' ? 'info' : 'secondary')); 
                                            ?>">
                                                <?php echo ucfirst(htmlspecialchars($domain['reason'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (isset($domain['confidence_score']) && $domain['confidence_score'] > 0): ?>
                                                <span class="badge bg-info">
                                                    <i class="fas fa-robot"></i> <?php echo $domain['confidence_score']; ?>%
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-question-circle"></i> N/A
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-flag"></i> <?php echo $domain['report_count']; ?> reports
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php echo $domain['report_count'] >= 10 ? 'bg-danger' : 'bg-warning'; ?>">
                                                <i class="fas fa-<?php echo $domain['report_count'] >= 10 ? 'ban' : 'exclamation-triangle'; ?>"></i>
                                                <?php echo $domain['report_count'] >= 10 ? 'Blacklisted' : 'Reported'; ?>
                                            </span>
                                        </td>
                                        <td><?php 
                                            $createdAt = $domain['added_at'] ?? null;
                                            if ($createdAt && strtotime($createdAt)) {
                                                echo '<i class="fas fa-calendar-alt"></i> ' . date('M d, Y H:i', strtotime($createdAt));
                                            } else {
                                                echo '<i class="fas fa-question-circle"></i> Unknown Date';
                                            }
                                        ?></td>
                                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                        <td>
                                            <form action="/url_phishing_project/public/blacklist" method="post" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to remove this domain from the blacklist?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i> Remove
                                                </button>
                                            </form>
                                        </td>
                                        <?php endif; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 