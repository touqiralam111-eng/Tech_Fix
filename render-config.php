<?php
// render-config.php - Database configuration for Render

// Check if running on Render
$is_render = isset($_SERVER['RENDER']) || getenv('RENDER') !== false;

if ($is_render) {
    // Get PostgreSQL connection string from Render
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
        // Fallback for Render
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

// Create connection function for PostgreSQL
function get_db_connection() {
    try {
        if (DB_DRIVER === 'pgsql') {
            // PostgreSQL connection for Render
            $pdo = new PDO(
                "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } else {
            // MySQL connection for local
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
            if ($conn->connect_error) {
                throw new Exception("Connection failed: " . $conn->connect_error);
            }
            return $conn;
        }
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return null;
    }
}

// Create global connection
$conn = get_db_connection();

if (!$conn) {
    die("We're experiencing technical difficulties. Please try again later.");
}
?>
