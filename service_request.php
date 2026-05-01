<?php
// service_request.php - Complete with File Upload & AJAX
include 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user_id = get_user_id();
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_type = sanitize_input($_POST['service_type']);
    $description = sanitize_input($_POST['description']);
    $urgency = sanitize_input($_POST['urgency']);
    $preferred_date = sanitize_input($_POST['preferred_date']);
    $budget = sanitize_input($_POST['budget']);
    
    // File upload handling
    $attachment = '';
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/services/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'zip'];
        $max_file_size = 10 * 1024 * 1024; // 10MB
        
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
                $error = "Attachment must be less than 10MB.";
            }
        } else {
            $error = "Only JPG, PNG, GIF, PDF, DOC, DOCX, and ZIP files are allowed.";
        }
    }
    
    if (empty($error)) {
        $sql = "INSERT INTO service_requests (user_id, service_type, description, urgency, preferred_date, budget, attachment) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("issssss", $user_id, $service_type, $description, $urgency, $preferred_date, $budget, $attachment);
            
            if ($stmt->execute()) {
                $request_id = $stmt->insert_id;
                $success = "Service request submitted successfully! Your request ID is #$request_id";
                
                // Log the service request
                $log_sql = "INSERT INTO site_visits (ip_address, user_agent, page_visited) VALUES (?, ?, 'service_request')";
                $log_stmt = $conn->prepare($log_sql);
                if ($log_stmt) {
                    $log_stmt->bind_param("ss", $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
                    $log_stmt->execute();
                    $log_stmt->close();
                }
                
                // Clear form
                $_POST = array();
            } else {
                $error = "Error submitting request: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Database error: " . $conn->error;
        }
    }
}

// Get user's service requests for display
$user_requests = $conn->query("
    SELECT * FROM service_requests 
    WHERE user_id = $user_id 
    ORDER BY created_at DESC 
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Request - TechFix | Professional IT Solutions</title>
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
                    <li><a href="service_request.php" class="active"><i class="fas fa-tools"></i> Services</a></li>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                </ul>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <span>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-tools"></i> Request IT Service</h2>
                    <p>Fill out the form below to request technical assistance</p>
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

                <form method="POST" action="" enctype="multipart/form-data" id="serviceRequestForm">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <!-- Left Column -->
                        <div>
                            <div class="form-group">
                                <label class="form-label">Service Type <span style="color: var(--danger);">*</span></label>
                                <select name="service_type" class="form-select" required>
                                    <option value="">Select Service Type</option>
                                    <option value="computer_repair" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'computer_repair' ? 'selected' : ''; ?>>Computer Repair</option>
                                    <option value="network_setup" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'network_setup' ? 'selected' : ''; ?>>Network Setup</option>
                                    <option value="security" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'security' ? 'selected' : ''; ?>>Security Solutions</option>
                                    <option value="cloud" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'cloud' ? 'selected' : ''; ?>>Cloud Services</option>
                                    <option value="data_recovery" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'data_recovery' ? 'selected' : ''; ?>>Data Recovery</option>
                                    <option value="mobile_support" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'mobile_support' ? 'selected' : ''; ?>>Mobile Support</option>
                                    <option value="consultation" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'consultation' ? 'selected' : ''; ?>>IT Consultation</option>
                                    <option value="other" <?php echo isset($_POST['service_type']) && $_POST['service_type'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Urgency Level <span style="color: var(--danger);">*</span></label>
                                <select name="urgency" class="form-select" required>
                                    <option value="low" <?php echo isset($_POST['urgency']) && $_POST['urgency'] == 'low' ? 'selected' : ''; ?>>Low - Can wait a few days</option>
                                    <option value="medium" <?php echo isset($_POST['urgency']) && $_POST['urgency'] == 'medium' ? 'selected' : ''; ?>>Medium - Within 24-48 hours</option>
                                    <option value="high" <?php echo isset($_POST['urgency']) && $_POST['urgency'] == 'high' ? 'selected' : ''; ?>>High - Need help today</option>
                                    <option value="critical" <?php echo isset($_POST['urgency']) && $_POST['urgency'] == 'critical' ? 'selected' : ''; ?>>Critical - Emergency service needed</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Preferred Service Date</label>
                                <input type="date" name="preferred_date" class="form-control" 
                                       min="<?php echo date('Y-m-d'); ?>"
                                       value="<?php echo isset($_POST['preferred_date']) ? htmlspecialchars($_POST['preferred_date']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Budget Estimate (₹)</label>
                                <input type="number" name="budget" class="form-control" placeholder="Enter your budget estimate"
                                       min="0" step="0.01"
                                       value="<?php echo isset($_POST['budget']) ? htmlspecialchars($_POST['budget']) : ''; ?>">
                            </div>
                        </div>
                        
                        <!-- Right Column -->
                        <div>
                            <div class="form-group">
                                <label class="form-label">Service Description <span style="color: var(--danger);">*</span></label>
                                <textarea name="description" class="form-control" placeholder="Please describe your issue in detail..." 
                                          rows="8" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                <small style="color: var(--gray); display: block; margin-top: 0.5rem;">
                                    Please provide as much detail as possible for faster resolution
                                </small>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Attachment (Optional)</label>
                                <div class="file-upload" onclick="document.getElementById('attachment').click()">
                                    <i class="fas fa-paperclip"></i>
                                    <p>Click to attach files</p>
                                    <small>Max size: 10MB (Images, Documents, ZIP)</small>
                                    <input type="file" id="attachment" name="attachment" 
                                           accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.zip" 
                                           style="display: none;" 
                                           onchange="updateFileInfo(this)">
                                </div>
                                <div id="fileInfo" style="margin-top: 0.5rem; font-size: 0.9rem; color: var(--gray);"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem;">
                            <i class="fas fa-paper-plane"></i> Submit Service Request
                        </button>
                    </div>
                </form>
            </div>

            <!-- Service Request History -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-history"></i> Your Service Requests</h2>
                </div>
                
                <?php if ($user_requests && $user_requests->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Service Type</th>
                                    <th>Description</th>
                                    <th>Urgency</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($request = $user_requests->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?php echo $request['id']; ?></td>
                                    <td><?php echo ucfirst(str_replace('_', ' ', $request['service_type'])); ?></td>
                                    <td><?php echo substr($request['description'], 0, 60) . (strlen($request['description']) > 60 ? '...' : ''); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $request['urgency']; ?>">
                                            <?php echo ucfirst($request['urgency']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo str_replace('_', '-', $request['status']); ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $request['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-outline" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                                onclick="viewRequestDetails(<?php echo $request['id']; ?>)">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <?php if ($request['status'] == 'pending'): ?>
                                            <button class="btn btn-danger" style="padding: 0.25rem 0.5rem; font-size: 0.8rem;"
                                                    onclick="cancelRequest(<?php echo $request['id']; ?>)">
                                                <i class="fas fa-times"></i> Cancel
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: var(--gray);">
                        <i class="fas fa-inbox fa-3x" style="margin-bottom: 1rem; opacity: 0.5;"></i>
                        <h3>No service requests yet</h3>
                        <p>Submit your first service request using the form above</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Service Pricing -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-dollar-sign"></i> Service Pricing Guide</h2>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div style="background: var(--light); padding: 1.5rem; border-radius: var(--radius); text-align: center;">
                        <h4 style="color: var(--primary);">Computer Repair</h4>
                        <p style="font-size: 1.5rem; font-weight: bold; color: var(--dark);">₹5000-₹8000</p>
                        <p style="color: var(--gray);">Virus removal, hardware repair, software installation</p>
                    </div>
                    <div style="background: var(--light); padding: 1.5rem; border-radius: var(--radius); text-align: center;">
                        <h4 style="color: var(--primary);">Network Setup</h4>
                        <p style="font-size: 1.5rem; font-weight: bold; color: var(--dark);">₹10000 - ₹22000</p>
                        <p style="color: var(--gray);">WiFi setup, router configuration, network security</p>
                    </div>
                    <div style="background: var(--light); padding: 1.5rem; border-radius: var(--radius); text-align: center;">
                        <h4 style="color: var(--primary);">Data Recovery</h4>
                        <p style="font-size: 1.5rem; font-weight: bold; color: var(--dark);">₹15000 - ₹28000</p>
                        <p style="color: var(--gray);">Hard drive recovery, RAID reconstruction, backup solutions</p>
                    </div>
                    <div style="background: var(--light); padding: 1.5rem; border-radius: var(--radius); text-align: center;">
                        <h4 style="color: var(--primary);">Consultation</h4>
                        <p style="font-size: 1.5rem; font-weight: bold; color: var(--dark);">₹7000/hour</p>
                        <p style="color: var(--gray);">IT strategy, system analysis, technology planning</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TechFix. All rights reserved. | Professional IT Solutions</p>
        </div>
    </footer>

    <!-- Request Details Modal -->
    <div id="requestModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: var(--radius); max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 1.5rem;">
                <h3>Request Details</h3>
                <button onclick="closeModal()" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--gray);">&times;</button>
            </div>
            <div id="requestDetails"></div>
        </div>
    </div>

    <script src="jsscript.js"></script>
    <script>
        function updateFileInfo(input) {
            const fileInfo = document.getElementById('fileInfo');
            if (input.files.length > 0) {
                const file = input.files[0];
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
        }

        function viewRequestDetails(requestId) {
            // AJAX call to get request details
            fetch(`get_request_details.php?id=${requestId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('requestDetails').innerHTML = `
                            <div style="margin-bottom: 1rem;">
                                <strong>Request ID:</strong> #${data.request.id}
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Service Type:</strong> ${data.request.service_type}
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Urgency:</strong> 
                                <span class="badge badge-${data.request.urgency}">${data.request.urgency}</span>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Status:</strong> 
                                <span class="badge badge-${data.request.status.replace('_', '-')}">${data.request.status}</span>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Description:</strong>
                                <p style="background: var(--light); padding: 1rem; border-radius: var(--radius); margin-top: 0.5rem;">
                                    ${data.request.description}
                                </p>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <strong>Created:</strong> ${data.request.created_at}
                            </div>
                            ${data.request.attachment ? `
                            <div style="margin-bottom: 1rem;">
                                <strong>Attachment:</strong> 
                                <a href="${data.request.attachment}" target="_blank" class="btn btn-outline" style="padding: 0.5rem 1rem;">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                            ` : ''}
                        `;
                        document.getElementById('requestModal').style.display = 'flex';
                    } else {
                        alert('Error loading request details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading request details');
                });
        }

        function closeModal() {
            document.getElementById('requestModal').style.display = 'none';
        }

        function cancelRequest(requestId) {
            if (confirm('Are you sure you want to cancel this service request?')) {
                fetch(`cancel_request.php?id=${requestId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Service request cancelled successfully');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error cancelling request');
                });
            }
        }

        // Real-time form validation
        document.getElementById('serviceRequestForm').addEventListener('submit', function(e) {
            const description = this.querySelector('[name="description"]').value;
            
            if (description.length < 20) {
                e.preventDefault();
                alert('Please provide a more detailed description (at least 20 characters)');
            }
        });

        // Character counter for description
        const descriptionField = document.querySelector('[name="description"]');
        const charCounter = document.createElement('div');
        charCounter.style.cssText = 'text-align: right; font-size: 0.8rem; color: var(--gray); margin-top: 0.5rem;';
        descriptionField.parentNode.appendChild(charCounter);

        descriptionField.addEventListener('input', function() {
            const length = this.value.length;
            charCounter.textContent = `${length} characters` + (length < 20 ? ' (minimum 20 required)' : '');
            charCounter.style.color = length < 20 ? 'var(--danger)' : 'var(--success)';
        });

        // Close modal when clicking outside
        document.getElementById('requestModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>