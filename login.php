<?php
// login.php - Complete with Sessions & Cookies
include 'config.php';

$error = '';

// Check if already logged in
if (is_logged_in()) {
    if (is_admin()) {
        redirect('admin_dashboard.php');
    } else {
        redirect('dashboard.php');
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = "Please enter both email and password!";
    } else {
        $sql = "SELECT id, username, email, password, user_type, is_active FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if (!$user['is_active']) {
                $error = "Your account has been deactivated. Please contact administrator.";
            } elseif (password_verify($password, $user['password'])) {
                // Login successful - Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['login_time'] = time();
                $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                
                // Set cookie if remember me is checked
                if ($remember) {
                    $cookie_token = bin2hex(random_bytes(32));
                    $cookie_expire = time() + (30 * 24 * 60 * 60); // 30 days
                    
                    // Store cookie token in database
                    $token_sql = "INSERT INTO user_sessions (user_id, session_id, login_time, ip_address) VALUES (?, ?, NOW(), ?)";
                    $token_stmt = $conn->prepare($token_sql);
                    $token_stmt->bind_param("iss", $user['id'], $cookie_token, $_SERVER['REMOTE_ADDR']);
                    $token_stmt->execute();
                    $token_stmt->close();
                    
                    // Set cookie
                    setcookie('remember_token', $cookie_token, $cookie_expire, "/", "", false, true);
                    setcookie('user_id', $user['id'], $cookie_expire, "/", "", false, true);
                }
                
                // Update last login
                $update_sql = "UPDATE users SET updated_at = NOW() WHERE id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("i", $user['id']);
                $update_stmt->execute();
                $update_stmt->close();
                
                // Log login activity
                $log_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, 'login_success')";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                $log_stmt->execute();
                $log_stmt->close();
                
                // Redirect based on user type
                if ($user['user_type'] == 'admin') {
                    redirect('admin_dashboard.php');
                } else {
                    redirect('dashboard.php');
                }
            } else {
                $error = "Invalid email or password!";
                
                // Log failed attempt
                $log_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, 'login_failed')";
                $log_stmt = $conn->prepare($log_sql);
                $log_stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                $log_stmt->execute();
                $log_stmt->close();
            }
        } else {
            $error = "No account found with this email!";
        }
        $stmt->close();
    }
}

// Check for remember me cookie
if (!is_logged_in() && isset($_COOKIE['remember_token']) && isset($_COOKIE['user_id'])) {
    $token = $_COOKIE['remember_token'];
    $user_id = intval($_COOKIE['user_id']);
    
    $sql = "SELECT u.id, u.username, u.email, u.user_type 
            FROM users u 
            JOIN user_sessions us ON u.id = us.user_id 
            WHERE u.id = ? AND us.session_id = ? AND us.logout_time IS NULL";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['login_time'] = time();
        
        if ($user['user_type'] == 'admin') {
            redirect('admin_dashboard.php');
        } else {
            redirect('dashboard.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechFix | Professional IT Solutions</title>
    <link rel="stylesheet" href="cssstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="login-page">
    <div class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-laptop-code"></i>Tech<span>Fix</span>
                </a>
                <a href="index.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="card-header" style="border: none; text-align: center;">
                    <h2 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-sign-in-alt"></i> Welcome Back
                    </h2>
                    <p style="color: var(--gray);">Sign in to your TechFix account</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Registration successful! Please login.
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['logout'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> You have been successfully logged out.
                    </div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <div style="position: relative;">
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <i class="fas fa-envelope" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div style="position: relative;">
                            <input type="password" name="password" class="form-control" placeholder="Enter your password" required
                                   id="passwordField">
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordField')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group" style="display: flex; justify-content: space-between; align-items: center;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remember" style="transform: scale(1.2);">
                            <span style="font-weight: 500;">Remember me</span>
                        </label>
                        <a href="forgot_password.php" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                            Forgot password?
                        </a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <p style="color: var(--gray);">Don't have an account? 
                        <a href="register.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                            Create one here
                        </a>
                    </p>
                </div>

                <!-- Demo Accounts -->
                <div style="background: rgba(67, 97, 238, 0.05); padding: 1.5rem; border-radius: var(--radius); margin-top: 2rem;">
                    <h4 style="margin-bottom: 1rem; color: var(--dark); display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-info-circle"></i> Demo Accounts
                    </h4>
                    <div style="font-size: 0.9rem; line-height: 1.6;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                            <span><strong>Admin Account:</strong></span>
                            <span>admin@techfix.com / admin123</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span><strong>User Account:</strong></span>
                            <span>user@techfix.com / user123</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="jsscript.js"></script>
    <script>
        // Additional login-specific JavaScript
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const email = this.querySelector('[name="email"]').value;
            const password = this.querySelector('[name="password"]').value;
            
            if (!email || !password) {
                e.preventDefault();
                techFixApp.showNotification('Please fill in all fields', 'error');
            }
        });

        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling.querySelector('i');
            
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>
</html>