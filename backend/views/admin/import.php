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

        .domain-input-area {
            border: 2px solid var(--primary-color);
            border-radius: 10px;
            padding: 2rem;
            background: var(--light-color);
            margin-bottom: 2rem;
        }

        .domain-input-area h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
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

        .scan-results {
            margin-top: 2rem;
        }

        .scan-results table {
            width: 100%;
            border-collapse: collapse;
        }

        .scan-results th,
        .scan-results td {
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
        }

        .scan-results th {
            background: var(--light-color);
            color: var(--dark-color);
            font-weight: 600;
        }

        .scan-results tr:hover {
            background: var(--light-color);
        }

        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .btn-import-all {
            margin-top: 1rem;
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
            <h2>Import Domains & Scan Reports</h2>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username'] ?? 'Admin'); ?>&background=4e73df&color=fff" alt="User Avatar">
            </div>
        </div>

        <div class="admin-section">
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-<?php echo $_SESSION['success'] ? 'success' : 'danger'; ?>" role="alert">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php 
                unset($_SESSION['success']);
                unset($_SESSION['message']);
            endif; 
            ?>

            <!-- Domain Import Section -->
            <div class="domain-input-area">
                <h3><i class="fas fa-globe"></i> Import Domains for Scanning</h3>
                <p class="text-muted">Enter multiple domains to scan and import into reports. Each domain will be scanned using R1 model and added to scan reports.</p>
                
                <form id="domainImportForm" method="post" action="/url_phishing_project/public/admin/import" autocomplete="off">
                    <div class="mb-3">
                        <label for="domains" class="form-label">Enter domains (comma or new line separated):</label>
                        <textarea class="form-control" id="domains" name="domains" rows="6" placeholder="example.com, test.com, phishing-site.com&#10;google.com&#10;github.com" required></textarea>
                        <div class="form-text">Enter one domain per line or separate with commas. Maximum 50 domains per import.</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="add_to_blacklist" name="add_to_blacklist" value="1">
                            <label class="form-check-label" for="add_to_blacklist">
                                Add phishing domains to blacklist automatically
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" id="scanButton">
                        <i class="fas fa-search"></i> Scan & Import Domains
                    </button>
                </form>
                
                <div class="loading-spinner" id="loadingSpinner">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Scanning domains... This may take a few minutes.</p>
                </div>
            </div>

            <!-- Scan Results Section -->
            <?php 
            // Only show scan results if they exist and we're not refreshing the page
            if (isset($_SESSION['scan_results']) && is_array($_SESSION['scan_results']) && count($_SESSION['scan_results']) > 0 && !isset($_GET['refresh'])) {
                $scanResults = $_SESSION['scan_results'];
            } elseif (isset($scanResults) && is_array($scanResults) && count($scanResults) > 0 && !isset($_GET['refresh'])) {
                // Keep existing scan results
            } else {
                $scanResults = [];
            }
            ?>
            
            <?php if (!empty($scanResults)): ?>
            <div class="scan-results">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3><i class="fas fa-list"></i> Scan Results (<?php echo count($scanResults); ?> domains)</h3>
                    <div>
                        <button class="btn btn-success btn-sm me-2" onclick="exportTableToExcel('scanResultsTable')">
                            <i class="fas fa-file-excel"></i> Export Excel
                        </button>
                        <button class="btn btn-danger btn-sm me-2" onclick="exportTableToPDF('scanResultsTable')">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </button>

                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="scanResultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Domain</th>
                                <th>ML Status</th>
                                <th>Confidence</th>
                                <th>Auto-Blacklisted</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($scanResults as $result): ?>
                                <tr class="scan-result-row">
                                    <td class="domain-name"><?php echo htmlspecialchars($result['domain']); ?></td>
                                    <td>
                                        <span class="badge status-badge <?php echo $result['status'] === 'phishing' ? 'bg-danger' : ($result['status'] === 'safe' ? 'bg-success' : 'bg-warning'); ?>">
                                            <?php echo ucfirst($result['status']); ?>
                                        </span>
                                        <?php if (isset($result['prediction']) && $result['prediction'] !== $result['status']): ?>
                                            <br><small class="text-muted">ML: <?php echo ucfirst($result['prediction']); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="confidence-score">
                                        <?php if (isset($result['confidence']) && $result['confidence'] !== 'N/A'): ?>
                                            <span class="badge bg-info"><?php echo $result['confidence']; ?>%</span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($result['auto_blacklisted']) && $result['auto_blacklisted']): ?>
                                            <span class="badge bg-warning">
                                                <i class="fas fa-shield-alt"></i> Auto-Added
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                        </tbody>
                    </table>
                    </div>
                
                <div class="mt-3">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5><?php echo count(array_filter($scanResults, function($r) { return $r['status'] === 'safe'; })); ?></h5>
                                    <small>Safe Domains</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5><?php echo count(array_filter($scanResults, function($r) { return $r['status'] === 'phishing'; })); ?></h5>
                                    <small>Phishing Domains</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5><?php echo count(array_filter($scanResults, function($r) { return $r['status'] === 'unknown'; })); ?></h5>
                                    <small>Unknown</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5><?php echo count($scanResults); ?></h5>
                                    <small>Total Scanned</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
                
                <!-- Note about detailed actions -->
                <div class="alert alert-info mt-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Note:</strong> For detailed domain analysis and actions (View Details, Back to Safe), please visit the 
                    <a href="/url_phishing_project/public/admin/reports" class="alert-link">Reports Section</a> 
                    where you can manage individual domains and their status.
                </div>
                <?php endif; ?>

            <!-- Instructions Section -->
            <div class="mt-4">
                <h4><i class="fas fa-info-circle"></i> Instructions</h4>
                <ul class="requirements-list">
                    <li><i class="fas fa-check"></i> Enter domains one per line or separate with commas</li>
                    <li><i class="fas fa-check"></i> Each domain will be scanned using ML model for accuracy</li>
                    <li><i class="fas fa-check"></i> WHOIS data is fetched using R1 system</li>
                    <li><i class="fas fa-check"></i> Phishing domains are automatically added to blacklist</li>
                    <li><i class="fas fa-check"></i> View detailed results in the reports section</li>
                    <li><i class="fas fa-check"></i> Export results to Excel or PDF format</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.1/jspdf.plugin.autotable.min.js"></script>
    <script>
        // Show loading spinner when form is submitted
        document.getElementById('domainImportForm').addEventListener('submit', function() {
            document.getElementById('loadingSpinner').style.display = 'block';
            document.getElementById('scanButton').disabled = true;
        });

        // Handle page refresh to hide scan results
        window.addEventListener('beforeunload', function() {
            // Add refresh parameter to URL
            if (window.location.search.indexOf('refresh') === -1) {
                window.location.search = window.location.search + (window.location.search ? '&' : '?') + 'refresh=1';
            }
        });

        // Check if page was refreshed and hide results
        if (window.location.search.indexOf('refresh') !== -1) {
            // Hide scan results section
            const scanResultsSection = document.querySelector('.scan-results');
            if (scanResultsSection) {
                scanResultsSection.style.display = 'none';
            }
        }

        // Export table to Excel
        function exportTableToExcel(tableID) {
            var wb = XLSX.utils.table_to_book(document.getElementById(tableID), {sheet:"Scan Results"});
            XLSX.writeFile(wb, 'domain_scan_results.xlsx');
        }

        // Export table to PDF
        function exportTableToPDF(tableID) {
            var { jsPDF } = window.jspdf;
            var doc = new jsPDF();
            var table = document.getElementById(tableID);
            var rows = table.querySelectorAll('tbody tr');
            var data = [];
            
            rows.forEach(function(row) {
                var rowData = [];
                row.querySelectorAll('td').forEach(function(cell, index) {
                    if (index < 2) { // Skip the Actions column
                    rowData.push(cell.innerText);
                    }
                });
                data.push(rowData);
            });
            
            doc.text('Domain Scan Results', 10, 10);
            doc.autoTable({
                head: [['Domain', 'Status']],
                body: data,
                startY: 20
            });
            doc.save('domain_scan_results.pdf');
        }

        // View domain details with ML results
        function viewDetails(domain, resultData) {
            // Create modal content with detailed information
            let modalContent = `
                <div class="modal fade" id="detailsModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-info-circle me-2"></i>Domain Details: ${domain}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-globe me-2"></i>Basic Information</h6>
                                        <table class="table table-sm">
                                            <tr><td><strong>Domain:</strong></td><td>${domain}</td></tr>
                                            <tr><td><strong>ML Status:</strong></td><td><span class="badge ${resultData.status === 'phishing' ? 'bg-danger' : (resultData.status === 'safe' ? 'bg-success' : 'bg-warning')}">${resultData.status.toUpperCase()}</span></td></tr>
                                            ${resultData.confidence ? `<tr><td><strong>ML Confidence:</strong></td><td><span class="badge bg-info">${resultData.confidence}%</span></td></tr>` : ''}
                                            ${resultData.prediction ? `<tr><td><strong>ML Prediction:</strong></td><td><span class="badge ${resultData.prediction === 'phishing' ? 'bg-danger' : 'bg-success'}">${resultData.prediction.toUpperCase()}</span></td></tr>` : ''}
                                            ${resultData.auto_blacklisted ? `<tr><td><strong>Auto-Blacklisted:</strong></td><td><span class="badge bg-warning">Yes</span></td></tr>` : ''}
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <h6><i class="fas fa-robot me-2"></i>ML Model Analysis</h6>
                                        ${resultData.scan_data && resultData.scan_data.expert_analysis ? `
                                            <div class="alert ${resultData.status === 'phishing' ? 'alert-danger' : 'alert-success'}">
                                                <strong>${resultData.scan_data.expert_analysis.summary || 'ML Analysis Complete'}</strong>
                                            </div>
                                            ${resultData.scan_data.expert_analysis.risk_factors && resultData.scan_data.expert_analysis.risk_factors.length > 0 ? `
                                                <div class="mt-2">
                                                    <strong>Risk Factors:</strong>
                                                    <ul class="list-unstyled">
                                                        ${resultData.scan_data.expert_analysis.risk_factors.map(factor => `<li><i class="fas fa-exclamation-triangle text-warning"></i> ${factor}</li>`).join('')}
                                                    </ul>
                                                </div>
                                            ` : ''}
                                            ${resultData.scan_data.expert_analysis.security_features && resultData.scan_data.expert_analysis.security_features.length > 0 ? `
                                                <div class="mt-2">
                                                    <strong>Security Features:</strong>
                                                    <ul class="list-unstyled">
                                                        ${resultData.scan_data.expert_analysis.security_features.map(feature => `<li><i class="fas fa-shield-alt text-success"></i> ${feature}</li>`).join('')}
                                                    </ul>
                                                </div>
                                            ` : ''}
                                        ` : '<p class="text-muted">No ML analysis available</p>'}
                                        
                                        ${resultData.auto_blacklisted ? `
                                            <div class="alert alert-warning mt-2">
                                                <i class="fas fa-shield-alt"></i> <strong>Auto-Blacklisted:</strong> This domain was automatically added to the blacklist as phishing.
                                            </div>
                                        ` : ''}
                                    </div>
                                </div>
            `;
            
            // Add WHOIS information if available
            if (resultData.whois_info) {
                modalContent += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-database me-2"></i>WHOIS Information</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Field</th>
                                                        <th>Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                `;
                
                for (const [key, value] of Object.entries(resultData.whois_info)) {
                    if (value && value !== 'Unknown') {
                        modalContent += `
                                                    <tr>
                                                        <td><strong>${key.replace(/([A-Z])/g, ' $1').trim()}</strong></td>
                                                        <td>${value}</td>
                                                    </tr>
                        `;
                    }
                }
                
                modalContent += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                `;
            }
            
            // Add ML features if available
            if (resultData.scan_data && resultData.scan_data.features) {
                modalContent += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-cogs me-2"></i>ML Model Features</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>Feature</th>
                                                        <th>Value</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                `;
                
                const featureDescriptions = {
                    'URL_Length': 'Total length of the URL',
                    'Domain_Length': 'Length of the domain name',
                    'Path_Length': 'Length of the URL path',
                    'Query_Length': 'Length of query parameters',
                    'Dots_in_Domain': 'Number of dots in domain',
                    'Contains_IP': 'Whether domain contains IP address',
                    'Contains_At': 'Whether URL contains @ symbol',
                    'Uses_HTTPS': 'Whether URL uses HTTPS',
                    'Has_Multiple_Subdomains': 'Whether domain has multiple subdomains',
                    'Contains_Hex': 'Whether URL contains hexadecimal characters',
                    'Contains_Numbers': 'Whether domain contains numbers',
                    'Contains_Special_Chars': 'Whether URL contains special characters',
                    'URL_Depth': 'Depth of URL path structure',
                    'Contains_Random_String': 'Whether URL contains random strings',
                    'Suspicious_TLD': 'Whether TLD is suspicious',
                    'Contains_Brand_Name': 'Whether domain contains brand names',
                    'Brand_Name_Count': 'Count of brand names found',
                    'Suspicious_Word_Count': 'Count of suspicious words',
                    'Entropy_Score': 'Entropy score of domain',
                    'Has_Redirect': 'Whether URL has redirects',
                    'Has_Shortener': 'Whether URL is shortened',
                    'Has_Typosquatting': 'Whether domain shows typosquatting',
                    'Has_Homograph': 'Whether domain shows homograph attacks'
                };
                
                for (const [key, value] of Object.entries(resultData.scan_data.features)) {
                    const description = featureDescriptions[key] || 'ML model feature';
                    modalContent += `
                                                    <tr>
                                                        <td><strong>${key.replace(/([A-Z])/g, ' $1').trim()}</strong></td>
                                                        <td>${value}</td>
                                                        <td><small class="text-muted">${description}</small></td>
                                                    </tr>
                    `;
                }
                
                modalContent += `
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                `;
            }
            
            // Add recommendations if available
            if (resultData.scan_data && resultData.scan_data.expert_analysis && resultData.scan_data.expert_analysis.recommendations) {
                modalContent += `
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <h6><i class="fas fa-lightbulb me-2"></i>Recommendations</h6>
                                        <ul class="list-group">
                `;
                
                resultData.scan_data.expert_analysis.recommendations.forEach(rec => {
                    modalContent += `
                                            <li class="list-group-item">
                                                <i class="fas fa-arrow-right text-primary me-2"></i>${rec}
                                            </li>
                    `;
                });
                
                modalContent += `
                                        </ul>
                                    </div>
                                </div>
                `;
            }
            
            modalContent += `
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-mdb-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-warning" onclick="backToSafe('${domain}', '${resultData.status}')">
                                    <i class="fas fa-undo"></i> Back to Safe
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Remove existing modal if any
            const existingModal = document.getElementById('detailsModal');
            if (existingModal) {
                existingModal.remove();
            }
            
            // Add modal to body and show it
            document.body.insertAdjacentHTML('beforeend', modalContent);
            const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
            modal.show();
            
            // Remove modal from DOM after it's hidden
            document.getElementById('detailsModal').addEventListener('hidden.bs.modal', function() {
                this.remove();
            });
        }

        // Add domain to blacklist
        function addToBlacklist(domain) {
            if (confirm('Are you sure you want to add ' + domain + ' to the blacklist?')) {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
                button.disabled = true;

                // Use AJAX to add to blacklist without page redirect
                fetch('/url_phishing_project/public/admin/blacklist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=add&domain=' + encodeURIComponent(domain) + '&reason=Added from import scan'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Update the button to show it's been added
                        button.innerHTML = '<i class="fas fa-check"></i> Added';
                        button.className = 'btn btn-sm btn-success';
                        button.disabled = true;
                    } else {
                        // Show error message
                        alert(data.message);
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to add domain to blacklist. Please try again.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        // Remove domain from blacklist
        function removeFromBlacklist(domain) {
            if (confirm('Are you sure you want to remove ' + domain + ' from the blacklist?')) {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Removing...';
                button.disabled = true;

                // Use AJAX to remove from blacklist without page redirect
                fetch('/url_phishing_project/public/admin/blacklist', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=remove&domain=' + encodeURIComponent(domain)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Update the button to show it's been removed
                        button.innerHTML = '<i class="fas fa-check"></i> Removed';
                        button.className = 'btn btn-sm btn-secondary';
                        button.disabled = true;
                    } else {
                        // Show error message
                        alert(data.message);
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to remove domain from blacklist. Please try again.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        // Import all results to reports
        function importAllToReports() {
            if (confirm('Are you sure you want to import all scan results to reports?')) {
                // This will be handled by the form submission
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = '/url_phishing_project/public/admin/import';
                
                var importInput = document.createElement('input');
                importInput.type = 'hidden';
                importInput.name = 'import_all';
                importInput.value = '1';
                
                form.appendChild(importInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Back to Safe function - Restore domain from blacklist and mark as safe
        function backToSafe(domain, currentStatus) {
            if (confirm('Are you sure you want to mark "' + domain + '" as safe? This will remove it from the blacklist if it\'s listed.')) {
                // Show loading state
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Restoring...';
                button.disabled = true;

                // Use AJAX to restore domain without page redirect
                fetch('/url_phishing_project/public/admin/import', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'action=restore&domain=' + encodeURIComponent(domain)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alert(data.message);
                        // Update the button to show it's been restored
                        button.innerHTML = '<i class="fas fa-check"></i> Restored';
                        button.className = 'btn btn-sm btn-success';
                        button.disabled = true;
                        
                        // Update the domain status in the results table
                        updateDomainStatus(domain, 'safe');
                    } else {
                        // Show error message
                        alert(data.message);
                        // Reset button
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to restore domain. Please try again.');
                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                });
            }
        }

        // Update domain status in the results table
        function updateDomainStatus(domain, newStatus) {
            // Find the domain row in the results table
            const rows = document.querySelectorAll('.scan-result-row');
            rows.forEach(row => {
                const domainCell = row.querySelector('.domain-name');
                if (domainCell && domainCell.textContent.trim() === domain) {
                    // Update status badge
                    const statusBadge = row.querySelector('.status-badge');
                    if (statusBadge) {
                        statusBadge.textContent = newStatus.toUpperCase();
                    }
                    
                    // Update confidence if available
                    const confidenceCell = row.querySelector('.confidence-score');
                    if (confidenceCell) {
                        confidenceCell.textContent = 'Updated';
                        confidenceCell.className = 'text-success fw-bold';
                    }
                }
            });
        }
    </script>
</body>
</html> 