<?php
// fix_database.php
session_start();

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'techfix';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Add missing columns
    $alter_queries = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20)",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS address TEXT",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255)",
        "ALTER TABLE login_logs ADD COLUMN IF NOT EXISTS user_agent TEXT",
        "ALTER TABLE service_requests ADD COLUMN IF NOT EXISTS assigned_admin INT",
        "ALTER TABLE service_requests ADD COLUMN IF NOT EXISTS completion_notes TEXT"
    ];
    
    foreach ($alter_queries as $query) {
        if ($conn->query($query) === TRUE) {
            echo "<div style='color: green; margin: 10px 0;'>✓ Success: " . substr($query, 0, 50) . "...</div>";
        } else {
            echo "<div style='color: red; margin: 10px 0;'>✗ Error: " . $conn->error . "</div>";
        }
    }
    
    echo "<h3 style='color: green;'>Database fixes applied successfully!</h3>";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>