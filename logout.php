<?php
// logout.php - Complete Session & Cookie Management
include 'config.php';

// Get user ID before destroying session
$user_id = get_user_id();

// Log logout activity
if ($user_id) {
    $log_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, 'logout')";
    $log_stmt = $conn->prepare($log_sql);
    $log_stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
    $log_stmt->execute();
    $log_stmt->close();
    
    // Update user sessions table
    if (isset($_COOKIE['remember_token'])) {
        $update_sql = "UPDATE user_sessions SET logout_time = NOW() WHERE user_id = ? AND session_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("is", $user_id, $_COOKIE['remember_token']);
        $update_stmt->execute();
        $update_stmt->close();
    }
}

// Destroy all session data
session_unset();
session_destroy();

// Delete remember me cookies
setcookie('remember_token', '', time() - 3600, "/");
setcookie('user_id', '', time() - 3600, "/");

// Delete all session-related cookies
if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $name = trim($parts[0]);
        setcookie($name, '', time() - 3600);
        setcookie($name, '', time() - 3600, '/');
    }
}

// Redirect to login page with logout message
header("Location: login.php?logout=1");
exit();
?>