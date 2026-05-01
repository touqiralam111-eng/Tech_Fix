<?php
// config.php - Fixed Version
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'techfix');

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        // Try to create database if it doesn't exist
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        $conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);
        $conn->select_db(DB_NAME);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}

// Create tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        user_type ENUM('user','admin') DEFAULT 'user',
        profile_image VARCHAR(255),
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS service_requests (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        service_type VARCHAR(100),
        description TEXT,
        urgency ENUM('low','medium','high','critical') DEFAULT 'medium',
        status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
        assigned_admin INT,
        completion_notes TEXT,
        preferred_date DATE,
        budget DECIMAL(10,2),
        attachment VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )",
    
    "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200),
        message TEXT NOT NULL,
        attachment VARCHAR(255),
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "CREATE TABLE IF NOT EXISTS site_visits (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ip_address VARCHAR(45),
        user_agent TEXT,
        visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        page_visited VARCHAR(255)
    )",
    
    "CREATE TABLE IF NOT EXISTS user_sessions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT,
        session_id VARCHAR(255),
        login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        logout_time TIMESTAMP NULL,
        ip_address VARCHAR(45),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )"
];

foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        // Ignore table creation errors for now
    }
}

// Create default admin if doesn't exist
$check_admin = $conn->query("SELECT id FROM users WHERE user_type='admin' LIMIT 1");
if ($check_admin->num_rows == 0) {
    $username = "admin";
    $email = "touqiralam111@gmail.com";
    $password = password_hash("admin123", PASSWORD_DEFAULT);
    
    $sql = "INSERT IGNORE INTO users (username, email, password, user_type) VALUES (?, ?, ?, 'admin')";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
        $stmt->close();
    }
}

// Track site visit
$ip = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];
$page = $_SERVER['PHP_SELF'];

$visit_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, ?)";
$stmt = $conn->prepare($visit_sql);
if ($stmt) {
    $stmt->bind_param("sss", $ip, $user_agent, $page);
    $stmt->execute();
    $stmt->close();
}

// Utility functions
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function get_user_id() {
    return $_SESSION['user_id'] ?? null;
}

function get_visit_count() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM site_visits");
    return $result ? $result->fetch_assoc()['count'] : 0;
}

function get_user_count() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    return $result ? $result->fetch_assoc()['count'] : 0;
}

// Safe query execution
function safe_query($sql, $params = []) {
    global $conn;
    $stmt = $conn->prepare($sql);
    if ($stmt && !empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    if ($stmt && $stmt->execute()) {
        return $stmt->get_result();
    }
    return false;
}
?>