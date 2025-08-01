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
    <title>My Profile - URL Phishing Detection</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/user.css">
</head>
<body>
    <header>
        <nav>
            <a href="/dashboard">Dashboard</a>
            <a href="/profile" class="active">My Profile</a>
            <a href="/scan-history">Scan History</a>
            <a href="/url_phishing_project/public/logout.php">Logout</a>
        </nav>
    </header>
    
    <main>
        <h1>My Profile</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-form">
            <form method="post" action="/profile/update">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>New Password (leave blank to keep current)</label>
                    <input type="password" name="new_password" minlength="8">
                </div>
                
                <div class="form-group">
                    <label>Current Password (required for any changes)</label>
                    <input type="password" name="current_password" required>
                </div>
                
                <button type="submit">Update Profile</button>
            </form>
        </div>
        
        <div class="api-keys">
            <h2>API Keys</h2>
            <table>
                <thead>
                    <tr>
                        <th>API Key</th>
                        <th>Status</th>
                        <th>Daily Limit</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($apiKeys as $key): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($key['api_key']); ?></td>
                        <td><?php echo $key['is_active'] ? 'Active' : 'Inactive'; ?></td>
                        <td><?php echo $key['daily_limit']; ?></td>
                        <td>
                            <form method="post" action="/api-key/toggle" class="inline-form">
                                <input type="hidden" name="key_id" value="<?php echo $key['id']; ?>">
                                <button type="submit" class="btn-toggle">
                                    <?php echo $key['is_active'] ? 'Disable' : 'Enable'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <form method="post" action="/api-key/generate" class="generate-key-form">
                <button type="submit">Generate New API Key</button>
            </form>
        </div>
    </main>
</body>
</html> 