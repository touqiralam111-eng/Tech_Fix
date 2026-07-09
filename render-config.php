<?php
// render-config.php - Database configuration for Render

// Get database URL from environment
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    // Parse Render's PostgreSQL URL
    $db_parts = parse_url($database_url);
    
    define('DB_HOST', $db_parts['host']);
    define('DB_USER', $db_parts['user']);
    define('DB_PASS', $db_parts['pass']);
    define('DB_NAME', ltrim($db_parts['path'], '/'));
    define('DB_PORT', $db_parts['port'] ?? '5432');
} else {
    // Fallback for local development
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'techfix');
    define('DB_PORT', '3306');
}

// Create connection
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
    
    if ($conn->connect_error) {
        // Try PostgreSQL if MySQL fails (for Render)
        if (getenv('DATABASE_URL')) {
            $conn = new PDO(
                "pgsql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS
            );
            echo "Connected to PostgreSQL successfully!";
        } else {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
    }
} catch (Exception $e) {
    // Log error but don't show to users
    error_log("Database error: " . $e->getMessage());
}
?>
