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
                    <h5 class="card-title">Add Domain to Blacklist</h5>
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
                            <button type="submit" class="btn btn-primary d-block w-100">Add Domain</button>
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
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Domain</th>
                            <th>Reason</th>
                            <th>Reports Count</th>
                            <th>Status</th>
                            <th>Added Date</th>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <th>Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($blacklistedDomains as $domain): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($domain['domain']); ?></td>
                            <td><?php echo htmlspecialchars($domain['reason']); ?></td>
                            <td>
                                <span class="badge bg-secondary">
                                    <?php echo $domain['report_count']; ?> reports
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $domain['report_count'] >= 10 ? 'bg-danger' : 'bg-warning'; ?>">
                                    <?php echo $domain['report_count'] >= 10 ? 'Blacklisted' : 'Reported'; ?>
                                </span>
                            </td>
                            <td><?php 
                                $createdAt = $domain['added_at'] ?? null;
                                if ($createdAt && strtotime($createdAt)) {
                                    echo date('M d, Y H:i', strtotime($createdAt));
                                } else {
                                    echo 'Unknown Date';
                                }
                            ?></td>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <td>
                                <form action="/url_phishing_project/public/blacklist" method="post" style="display: inline;" 
                                      onsubmit="return confirm('Are you sure you want to remove this domain from the blacklist?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="domain_id" value="<?php echo $domain['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 