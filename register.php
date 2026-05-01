<?php
// register.php - Complete with File Upload
include 'config.php';

$error = '';
$success = '';

// Check if already logged in
if (is_logged_in()) {
    redirect('dashboard.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = sanitize_input($_POST['phone']);
    $address = sanitize_input($_POST['address']);
    $user_type = 'user'; // Default to user
    
    // File upload handling
    $profile_image = '';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2MB
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            if ($_FILES['profile_image']['size'] <= $max_file_size) {
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
                    $profile_image = $file_path;
                } else {
                    $error = "Failed to upload profile image.";
                }
            } else {
                $error = "Profile image must be less than 2MB.";
            }
        } else {
            $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
        }
    }
    
    // Validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "All required fields must be filled!";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address!";
    } elseif (strlen($username) < 3) {
        $error = "Username must be at least 3 characters!";
    } else {
        try {
            // Check if user exists
            $check_sql = "SELECT id FROM users WHERE email = ? OR username = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param("ss", $email, $username);
            $check_stmt->execute();
            $check_stmt->store_result();
            
            if ($check_stmt->num_rows > 0) {
                $error = "User with this email or username already exists!";
            } else {
                // Insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username, email, password, phone, address, profile_image, user_type) 
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("sssssss", $username, $email, $hashed_password, $phone, $address, $profile_image, $user_type);
                
                if ($stmt->execute()) {
                    $user_id = $stmt->insert_id;
                    
                    // Auto login after registration
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['user_type'] = $user_type;
                    $_SESSION['login_time'] = time();
                    
                    // Log registration
                    $log_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, 'registration')";
                    $log_stmt = $conn->prepare($log_sql);
                    $log_stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                    $log_stmt->execute();
                    $log_stmt->close();
                    
                    $success = "Registration successful! Redirecting to dashboard...";
                    header("refresh:2;url=dashboard.php");
                } else {
                    $error = "Registration failed: " . $stmt->error;
                }
            }
            
            $check_stmt->close();
        } catch (Exception $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TechFix | Professional IT Solutions</title>
    <link rel="stylesheet" href="cssstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="register-page">
    <div class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-laptop-code"></i>Tech<span>Fix</span>
                </a>
                <a href="login.php" class="btn btn-outline">
                    <i class="fas fa-sign-in-alt"></i> Login
                </a>
            </nav>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="form-container">
                <div class="card-header" style="border: none; text-align: center;">
                    <h2 style="color: var(--primary); margin-bottom: 0.5rem;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </h2>
                    <p style="color: var(--gray);">Join thousands of satisfied customers</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data" id="registerForm">
                    <!-- Profile Image Upload -->
                    <div class="form-group">
                        <label class="form-label">Profile Picture (Optional)</label>
                        <div class="file-upload" onclick="document.getElementById('profileImage').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload profile picture</p>
                            <small>Max size: 2MB (JPG, PNG, GIF)</small>
                            <input type="file" id="profileImage" name="profile_image" accept="image/*" style="display: none;" 
                                   onchange="previewImage(this)">
                        </div>
                        <div id="imagePreview" style="display: none; margin-top: 1rem; text-align: center;">
                            <img id="preview" src="#" alt="Preview" style="max-width: 150px; max-height: 150px; border-radius: 50%;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Username <span style="color: var(--danger);">*</span></label>
                        <div style="position: relative;">
                            <input type="text" name="username" class="form-control" placeholder="Choose a username" required
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                                   minlength="3" maxlength="50">
                            <i class="fas fa-user" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address <span style="color: var(--danger);">*</span></label>
                        <div style="position: relative;">
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required
                                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            <i class="fas fa-envelope" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <div style="position: relative;">
                            <input type="tel" name="phone" class="form-control" placeholder="Enter your phone number"
                                   value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                            <i class="fas fa-phone" style="position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: var(--gray);"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" placeholder="Enter your address" 
                                  rows="3"><?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Password <span style="color: var(--danger);">*</span></label>
                        <div style="position: relative;">
                            <input type="password" name="password" class="form-control" placeholder="Create a password" required
                                   minlength="6" id="passwordField">
                            <button type="button" class="password-toggle" onclick="togglePassword('passwordField')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small style="color: var(--gray); margin-top: 0.5rem; display: block;">
                            Password must be at least 6 characters with uppercase, lowercase, and numbers
                        </small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Confirm Password <span style="color: var(--danger);">*</span></label>
                        <div style="position: relative;">
                            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password" required
                                   id="confirmPasswordField">
                            <button type="button" class="password-toggle" onclick="togglePassword('confirmPasswordField')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="terms" required style="margin-top: 0.25rem; transform: scale(1.2);">
                            <span style="font-weight: 500;">
                                I agree to the 
                                <a href="#" style="color: var(--primary);">Terms of Service</a> 
                                and 
                                <a href="#" style="color: var(--primary);">Privacy Policy</a>
                            </span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
                    <p style="color: var(--gray);">
                        Already have an account? 
                        <a href="login.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">
                            Sign in here
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script src="jsscript.js"></script>
    <script>
        function previewImage(input) {
            const preview = document.getElementById('preview');
            const previewContainer = document.getElementById('imagePreview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

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

        // Real-time password strength check
        document.getElementById('passwordField').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = checkPasswordStrength(password);
            updatePasswordStrength(strength);
        });

        function checkPasswordStrength(password) {
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]+/)) strength++;
            if (password.match(/[A-Z]+/)) strength++;
            if (password.match(/[0-9]+/)) strength++;
            if (password.match(/[!@#$%^&*(),.?":{}|<>]+/)) strength++;
            
            return strength;
        }

        function updatePasswordStrength(strength) {
            const strengthBar = document.getElementById('passwordStrength') || createStrengthBar();
            const strengthText = document.getElementById('passwordStrengthText') || createStrengthText();
            
            const colors = ['#f72585', '#f8961e', '#f8961e', '#4cc9f0', '#4cc9f0'];
            const texts = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong'];
            
            strengthBar.style.width = `${(strength / 5) * 100}%`;
            strengthBar.style.background = colors[strength - 1] || '#f72585';
            strengthText.textContent = texts[strength - 1] || 'Very Weak';
            strengthText.style.color = colors[strength - 1] || '#f72585';
        }

        function createStrengthBar() {
            const container = document.createElement('div');
            container.style.cssText = 'margin-top: 0.5rem; background: #f0f0f0; border-radius: 10px; height: 6px; overflow: hidden;';
            
            const bar = document.createElement('div');
            bar.id = 'passwordStrength';
            bar.style.cssText = 'height: 100%; width: 0%; transition: all 0.3s ease; border-radius: 10px;';
            
            container.appendChild(bar);
            
            const text = document.createElement('div');
            text.id = 'passwordStrengthText';
            text.style.cssText = 'font-size: 0.8rem; font-weight: 600; margin-top: 0.25rem;';
            text.textContent = 'Very Weak';
            
            const passwordGroup = document.querySelector('[name="password"]').parentNode.parentNode;
            passwordGroup.appendChild(container);
            passwordGroup.appendChild(text);
            
            return bar;
        }

        function createStrengthText() {
            return document.getElementById('passwordStrengthText');
        }
    </script>
</body>
</html>