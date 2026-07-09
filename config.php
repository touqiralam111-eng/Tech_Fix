<?php
// config.php - Render Compatible Version

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors on live

// Auto-detect environment
$is_render = isset($_SERVER['RENDER']) || getenv('RENDER') !== false;

if ($is_render) {
    // Render PostgreSQL configuration
    $database_url = getenv('DATABASE_URL');
    
    if ($database_url) {
        $db_parts = parse_url($database_url);
        
        define('DB_HOST', $db_parts['host']);
        define('DB_USER', $db_parts['user']);
        define('DB_PASS', $db_parts['pass']);
        define('DB_NAME', ltrim($db_parts['path'], '/'));
        define('DB_PORT', $db_parts['port'] ?? '5432');
        define('DB_DRIVER', 'pgsql');
    } else {
        // Fallback
        define('DB_HOST', 'localhost');
        define('DB_USER', 'postgres');
        define('DB_PASS', '');
        define('DB_NAME', 'techfix');
        define('DB_PORT', '5432');
        define('DB_DRIVER', 'pgsql');
    }
} else {
    // Local development (XAMPP)
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'techfix');
    define('DB_PORT', '3306');
    define('DB_DRIVER', 'mysql');
}

// Create connection
try {
    if (DB_DRIVER === 'pgsql') {
        // PostgreSQL for Render
        $conn = new PDO(
            "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
            DB_USER,
            DB_PASS
        );
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } else {
        // MySQL for local development
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
    }
} catch (Exception $e) {
    if ($is_render) {
        error_log("Database error: " . $e->getMessage());
        die("We're experiencing technical difficulties. Please try again later.");
    } else {
        die("Database error: " . $e->getMessage());
    }
}

// Utility functions (Keep these the same)
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
    if (DB_DRIVER === 'pgsql') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM site_visits");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } else {
        $result = $conn->query("SELECT COUNT(*) as count FROM site_visits");
        return $result ? $result->fetch_assoc()['count'] : 0;
    }
}

function get_user_count() {
    global $conn;
    if (DB_DRIVER === 'pgsql') {
        $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    } else {
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        return $result ? $result->fetch_assoc()['count'] : 0;
    }
}

// Safe query execution (modified for PDO)
function safe_query($sql, $params = []) {
    global $conn;
    
    if (DB_DRIVER === 'pgsql') {
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->execute($params);
            return $stmt;
        }
        return false;
    } else {
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
}
?>
