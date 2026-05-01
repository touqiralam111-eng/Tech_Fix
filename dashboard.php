<?php
session_start();

// Simple database simulation (replace with real DB in production)
$users_file = 'users.json';

// Handle actions
$action = $_GET['action'] ?? 'login';

switch($action) {
    case 'login': handleLogin(); break;
    case 'register': handleRegister(); break;
    case 'dashboard': showDashboard(); break;
    case 'logout': handleLogout(); break;
    default: showLoginForm();
}

function handleLogin() {
    if ($_POST) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $users = getUsers();
        if (isset($users[$email]) && password_verify($password, $users[$email]['password'])) {
            $_SESSION['user'] = $users[$email];
            header("Location: 13.php?action=dashboard");
            exit();
        } else {
            showLoginForm("Invalid credentials!");
        }
    } else {
        showLoginForm();
    }
}

function handleRegister() {
    if ($_POST) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        
        if ($password !== $confirm) {
            showRegisterForm("Passwords don't match!");
            return;
        }
        
        $users = getUsers();
        if (isset($users[$email])) {
            showRegisterForm("Email already exists!");
        } else {
            $users[$email] = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT)
            ];
            saveUsers($users);
            showLoginForm("Registration successful! Please login.");
        }
    } else {
        showRegisterForm();
    }
}

function handleLogout() {
    session_destroy();
    header("Location: 13.php");
    exit();
}

function showDashboard() {
    if (!isset($_SESSION['user'])) {
        header("Location: 13.php");
        exit();
    }
    $user = $_SESSION['user'];
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Dashboard</title></head>
    <body style="font-family: Arial; margin: 40px;">
        <div style="max-width: 500px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
            <h2>Welcome, <?= $user['name'] ?>!</h2>
            <p><strong>Email:</strong> <?= $user['email'] ?></p>
            <p><strong>Login Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
            <a href="13.php?action=logout" style="color: red; text-decoration: none;">Logout</a>
        </div>
    </body>
    </html>
    <?php
}

function showLoginForm($error = '') {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Login</title></head>
    <body style="font-family: Arial; margin: 40px;">
        <div style="max-width: 300px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
            <h2>Login</h2>
            <?php if ($error): ?><div style="color: red;"><?= $error ?></div><?php endif; ?>
            <form method="POST" action="13.php?action=login">
                <input type="email" name="email" placeholder="Email" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <input type="password" name="password" placeholder="Password" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <button type="submit" style="width: 100%; padding: 10px; background: blue; color: white; border: none;">Login</button>
            </form>
            <p style="text-align: center;"><a href="13.php?action=register">Create account</a></p>
        </div>
    </body>
    </html>
    <?php
}

function showRegisterForm($error = '') {
    ?>
    <!DOCTYPE html>
    <html>
    <head><title>Register</title></head>
    <body style="font-family: Arial; margin: 40px;">
        <div style="max-width: 300px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
            <h2>Register</h2>
            <?php if ($error): ?><div style="color: red;"><?= $error ?></div><?php endif; ?>
            <form method="POST" action="13.php?action=register">
                <input type="text" name="name" placeholder="Full Name" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <input type="email" name="email" placeholder="Email" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <input type="password" name="password" placeholder="Password" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required style="width: 100%; margin: 5px 0; padding: 8px;"><br>
                <button type="submit" style="width: 100%; padding: 10px; background: green; color: white; border: none;">Register</button>
            </form>
            <p style="text-align: center;"><a href="13.php">Back to login</a></p>
        </div>
    </body>
    </html>
    <?php
}

// Helper functions for file-based user storage
function getUsers() {
    global $users_file;
    if (file_exists($users_file)) {
        return json_decode(file_get_contents($users_file), true) ?: [];
    }
    return [];
}

function saveUsers($users) {
    global $users_file;
    file_put_contents($users_file, json_encode($users));
}
?>