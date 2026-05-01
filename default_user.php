<?php
include 'config.php';

echo "<h3>Creating Default Users</h3>";

$users = [
    [
        'username' => 'admin',
        'email' => 'admin@techfix.com',
        'password' => 'admin123',
        'user_type' => 'admin'
    ],
    [
        'username' => 'user1',
        'email' => 'user@techfix.com',
        'password' => 'user123',
        'user_type' => 'user'
    ]
];

foreach ($users as $user) {
    // Check if user exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $user['email']);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows == 0) {
        // Insert user
        $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, user_type) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $user['username'], $user['email'], $hashed_password, $user['user_type']);
        
        if ($stmt->execute()) {
            echo "✓ Created {$user['user_type']}: {$user['email']} / {$user['password']}<br>";
        } else {
            echo "✗ Failed to create {$user['email']}: " . $stmt->error . "<br>";
        }
        
        $stmt->close();
    } else {
        echo "✓ User {$user['email']} already exists<br>";
    }
    
    $check_stmt->close();
}

echo "<h4>Default Users Ready!</h4>";
echo '<a href="login.php">Go to Login</a>';
?>