<?php
// contact.php - Complete with Email Functionality
include 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $subject = sanitize_input($_POST['subject']);
    $message = sanitize_input($_POST['message']);
    $priority = sanitize_input($_POST['priority']);
    
    // File upload handling
    $attachment = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/contacts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array(strtolower($file_extension), $allowed_extensions)) {
            if ($_FILES['attachment']['size'] <= $max_file_size) {
                $filename = uniqid() . '_' . time() . '.' . $file_extension;
                $file_path = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $file_path)) {
                    $attachment = $file_path;
                } else {
                    $error = "Failed to upload attachment.";
                }
            } else {
                $error = "Attachment must be less than 5MB.";
            }
        } else {
            $error = "Only JPG, PNG, GIF, PDF, DOC, and DOCX files are allowed.";
        }
    }
    
    if (empty($error)) {
        // Insert into database
        $sql = "INSERT INTO contacts (name, email, subject, message, attachment) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $name, $email, $subject, $message, $attachment);
        
        if ($stmt->execute()) {
            // Send email notification
            if (sendContactEmail($name, $email, $subject, $message, $priority, $attachment)) {
                $success = "Thank you for your message! We'll get back to you within 24 hours.";
            } else {
                $success = "Thank you for your message! We've received it and will respond soon.";
            }
            
            // Clear form
            $_POST = array();
        } else {
            $error = "Sorry, there was an error sending your message. Please try again.";
        }
        $stmt->close();
    }
}

function sendContactEmail($name, $email, $subject, $message, $priority, $attachment) {
    $to = "support@techfix.com";
    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    $email_subject = "[$priority] $subject - TechFix Contact Form";
    
    $email_message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #4361ee; color: white; padding: 20px; text-align: center; }
            .content { background: #f8f9fa; padding: 20px; }
            .footer { background: #212529; color: white; padding: 10px; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>New Contact Form Submission</h2>
                <p>Priority: $priority</p>
            </div>
            <div class='content'>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
                " . ($attachment ? "<p><strong>Attachment:</strong> File attached</p>" : "") . "
            </div>
            <div class='footer'>
                <p>This email was sent from the TechFix contact form</p>
            </div>
        </div>
    </body>
    </html>
    ";
    
    return mail($to, $email_subject, $email_message, $headers);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - TechFix | Professional IT Solutions</title>
    <link rel="stylesheet" href="cssstyle.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="container">
            <nav class="navbar">
                <a href="index.php" class="logo">
                    <i class="fas fa-laptop-code"></i>Tech<span>Fix</span>
                </a>
                <ul class="nav-links">
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="service_request.php"><i class="fas fa-tools"></i> Services</a></li>
                    <li><a href="contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <?php endif; ?>
                </ul>
                <div class="user-menu">
                    <?php if (is_logged_in()): ?>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-envelope"></i> Contact Us</h2>
                    <p>Get in touch with our support team. We're here to help!</p>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: start;">
                    <!-- Contact Form -->
                    <div>
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

                        <form method="POST" action="" enctype="multipart/form-data" id="contactForm">
                            <div class="form-group">
                                <label class="form-label">Full Name <span style="color: var(--danger);">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="Enter your full name" required
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Email Address <span style="color: var(--danger);">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="Enter your email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Subject <span style="color: var(--danger);">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="Enter subject" required
                                       value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Priority</label>
                                <select name="priority" class="form-select">
                                    <option value="Low">Low</option>
                                    <option value="Normal" selected>Normal</option>
                                    <option value="High">High</option>
                                    <option value="Urgent">Urgent</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Message <span style="color: var(--danger);">*</span></label>
                                <textarea name="message" class="form-control" placeholder="Describe your issue or inquiry..." 
                                          rows="6" required><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Attachment (Optional)</label>
                                <div class="file-upload" onclick="document.getElementById('attachment').click()">
                                    <i class="fas fa-paperclip"></i>
                                    <p>Click to attach file</p>
                                    <small>Max size: 5MB (Images, PDF, DOC)</small>
                                    <input type="file" id="attachment" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx" style="display: none;">
                                </div>
                                <div id="fileInfo" style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--gray);"></div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </form>
                    </div>
                    
                    <!-- Contact Information -->
                    <div>
                        <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 2rem; border-radius: var(--radius); margin-bottom: 2rem;">
                            <h3 style="margin-bottom: 1rem;"><i class="fas fa-headset"></i> Support Center</h3>
                            <p>Our team is available 24/7 to assist you with any technical issues or inquiries.</p>
                        </div>
                        
                        <div class="features-grid">
                            <div class="feature-item">
                                <i class="fas fa-phone"></i>
                                <h4>Call Us</h4>
                                <p>+91 8511726065<br><small>24/7 Support Line</small></p>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-envelope"></i>
                                <h4>Email Us</h4>
                                <p>support@techfix.com<br><small>Response within 1 hour</small></p>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-clock"></i>
                                <h4>Business Hours</h4>
                                <p>Mon - Sun: 24/7<br><small>Emergency support available</small></p>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <h4>Visit Us</h4>
                                <p>Rampura Petrol Pump Main Road
Surat, Gujarat 395003<br><small></small></p>
                            </div>
                        </div>
                        
                        <!-- Live Chat Widget -->
                        <div style="background: var(--light); padding: 1.5rem; border-radius: var(--radius); margin-top: 2rem; text-align: center;">
                            <i class="fas fa-comments" style="font-size: 2rem; color: var(--primary); margin-bottom: 1rem;"></i>
                            <h4>Live Chat Support</h4>
                            <p style="margin-bottom: 1rem;">Chat with our support team in real-time</p>
                            <button class="btn btn-primary" onclick="openLiveChat()">
                                <i class="fas fa-comment-dots"></i> Start Live Chat
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TechFix. All rights reserved. | Professional IT Solutions & Support</p>
            <div class="footer-links">
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
                <a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
                <a href="#"><i class="fas fa-file-contract"></i> Terms of Service</a>
            </div>
        </div>
    </footer>

    <script src="jsscript.js"></script>
    <script>
        // File upload handling
        document.getElementById('attachment').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const fileInfo = document.getElementById('fileInfo');
            
            if (file) {
                const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                fileInfo.innerHTML = `
                    <i class="fas fa-file"></i> 
                    <strong>${file.name}</strong> (${fileSize} MB)
                    <span style="color: var(--success);">
                        <i class="fas fa-check"></i> Ready to upload
                    </span>
                `;
            } else {
                fileInfo.innerHTML = '';
            }
        });

        function openLiveChat() {
            alert('Live chat feature would be integrated here with a service like Intercom or LiveChat');
            // In a real implementation, this would open a chat widget
        }

        // Form validation
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const message = this.querySelector('[name="message"]').value;
            
            if (message.length < 10) {
                e.preventDefault();
                techFixApp.showNotification('Please enter a message with at least 10 characters', 'error');
            }
        });

        // Character counter for message
        const messageField = document.querySelector('[name="message"]');
        const charCounter = document.createElement('div');
        charCounter.style.cssText = 'text-align: right; font-size: 0.8rem; color: var(--gray); margin-top: 0.5rem;';
        messageField.parentNode.appendChild(charCounter);

        messageField.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = `${length} characters` + (length < 10 ? ' (minimum 10 required)' : '');
            charCounter.style.color = length < 10 ? 'var(--danger)' : 'var(--success)';
        });
    </script>
</body>
</html>