<?php
session_start();
// Destroy session and clear session cookie
session_unset();
session_destroy();
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params['path'], $params['domain'],
        $params['secure'], $params['httponly']
    );
}
header('Location: /url_phishing_project/public/');
exit; 