
<?php

session_start();

// Clear all session variables
$_SESSION = [];

// Remove the session cookie
if (ini_get("session.use_cookies")) {
    $p = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 36000,
        $p['path'],
        $p['domain'],
        $p['secure'],
        $p['httponly']
    );
}

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
