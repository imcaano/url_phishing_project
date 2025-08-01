<?php
if (!isset($_SESSION['user_id'])) {
    header('Location: /url_phishing_project/public/login');
    exit;
}
if ($_SESSION['role'] !== 'user') {
    header('Location: /url_phishing_project/public/admin/dashboard');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - URL Phishing Detection</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
    <header>
        <nav>
            <a href="/dashboard" class="active">Dashboard</a>
            <a href="/profile">My Profile</a>
            <a href="/scan-history">Scan History</a>
            <a href="/url_phishing_project/public/logout.php">Logout</a>
        </nav>
    </header>
    
    <main>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></h1>
        
        <div class="scan-box">
            <h2>Scan URL</h2>
            <form action="/scan" method="post" id="scanForm">
                <input type="url" name="url" placeholder="Enter URL to scan" required>
                <button type="submit">Scan for Phishing</button>
            </form>
        </div>
        
        <div class="recent-scans">
            <h2>Recent Scans</h2>
            <table>
                <thead>
                    <tr>
                        <th>URL</th>
                        <th>Result</th>
                        <th>Confidence</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentScans as $scan): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($scan['url']); ?></td>
                        <td class="<?php echo $scan['is_phishing'] ? 'phishing' : 'safe'; ?>">
                            <?php echo $scan['is_phishing'] ? 'Phishing' : 'Safe'; ?>
                        </td>
                        <td><?php echo number_format($scan['confidence_score'], 2); ?>%</td>
                        <td><?php echo date('Y-m-d H:i', strtotime($scan['scan_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html> 