<?php
// install.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Install TechFix</title>
    <link rel="stylesheet" href="cssstyle.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <a href="index.php" class="logo">Tech<span>Fix</span></a>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="card" style="max-width: 800px; margin: 0 auto;">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-cogs"></i> TechFix Installation</h2>
                </div>
                
                <div style="padding: 2rem;">
                    <?php
                    // Database setup
                    $host = 'localhost';
                    $user = 'root';
                    $pass = '';
                    $dbname = 'techfix';
                    
                    try {
                        // Create connection
                        $conn = new mysqli($host, $user, $pass);
                        
                        if ($conn->connect_error) {
                            throw new Exception("Connection failed: " . $conn->connect_error);
                        }
                        
                        // Create database
                        if ($conn->query("CREATE DATABASE IF NOT EXISTS $dbname") === TRUE) {
                            echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Database created successfully</div>";
                        } else {
                            throw new Exception("Error creating database: " . $conn->error);
                        }
                        
                        // Select database
                        $conn->select_db($dbname);
                        
                        // Include config to create tables
                        include 'config.php';
                        
                        echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Tables created successfully</div>";
                        echo "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Default admin user created</div>";
                        
                        echo "<h3 style='color: var(--success); margin: 2rem 0;'>Installation Complete!</h3>";
                        echo "<p>Your TechFix application has been successfully installed.</p>";
                        
                        echo "<div style='background: #f8f9fa; padding: 1.5rem; border-radius: var(--radius); margin: 2rem 0;'>";
                        echo "<h4><i class='fas fa-user-shield'></i> Default Admin Account</h4>";
                        echo "<p><strong>Email:</strong> admin@techfix.com</p>";
                        echo "<p><strong>Password:</strong> admin123</p>";
                        echo "</div>";
                        
                        echo "<div class='text-center' style='margin-top: 2rem;'>";
                        echo "<a href='login.php' class='btn btn-primary' style='margin-right: 1rem;'><i class='fas fa-sign-in-alt'></i> Go to Login</a>";
                        echo "<a href='index.php' class='btn btn-outline'><i class='fas fa-home'></i> Visit Homepage</a>";
                        echo "</div>";
                        
                    } catch (Exception $e) {
                        echo "<div class='alert alert-error'><i class='fas fa-exclamation-triangle'></i> " . $e->getMessage() . "</div>";
                        echo "<p>Please check your database configuration in config.php</p>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>