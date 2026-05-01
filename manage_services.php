<?php
include 'check_login.php';
$user = check_login();

// Check if user is admin
if ($user['user_type'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle service status updates
if (isset($_POST['update_status'])) {
    $service_id = intval($_POST['service_id']);
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE service_requests SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_status, $service_id);
    
    if ($stmt->execute()) {
        $message = "Service status updated successfully";
    } else {
        $error = "Error updating service: " . $stmt->error;
    }
}

// Get all service requests
$services_sql = "SELECT sr.*, u.username, u.email 
                FROM service_requests sr 
                JOIN users u ON sr.user_id = u.id 
                ORDER BY sr.created_at DESC";
$services_result = $conn->query($services_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Services - Admin Panel</title>
    <link rel="stylesheet" href="cssstyle.css">
    <style>
        .service-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .service-table th, .service-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .service-table th {
            background: #35424a;
            color: white;
        }
        
        .status-pending { color: #ff9800; font-weight: bold; }
        .status-in_progress { color: #2196f3; font-weight: bold; }
        .status-completed { color: #4caf50; font-weight: bold; }
        
        .urgency-low { color: #4caf50; }
        .urgency-medium { color: #ff9800; }
        .urgency-high { color: #f44336; }
        .urgency-critical { color: #d32f2f; font-weight: bold; }
        
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Service Requests</h2>
        <div>
            <a href="admin_dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="manage_users.php" style="color: white; margin-right: 15px;">Users</a>
            <a href="reports.php" style="color: white; margin-right: 15px;">Reports</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <?php if (isset($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="service-stats">
            <h3>Service Requests Summary</h3>
            <?php
            $total_services = $conn->query("SELECT COUNT(*) as count FROM service_requests")->fetch_assoc()['count'];
            $pending_services = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='pending'")->fetch_assoc()['count'];
            $inprogress_services = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='in_progress'")->fetch_assoc()['count'];
            $completed_services = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='completed'")->fetch_assoc()['count'];
            ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Requests</h4>
                    <p><?php echo $total_services; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Pending</h4>
                    <p class="status-pending"><?php echo $pending_services; ?></p>
                </div>
                <div class="stat-card">
                    <h4>In Progress</h4>
                    <p class="status-in_progress"><?php echo $inprogress_services; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Completed</h4>
                    <p class="status-completed"><?php echo $completed_services; ?></p>
                </div>
            </div>
        </div>

        <div class="services-list">
            <h3>All Service Requests</h3>
            <?php if ($services_result->num_rows > 0): ?>
                <table class="service-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Service Type</th>
                            <th>Description</th>
                            <th>Urgency</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($service = $services_result->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $service['id']; ?></td>
                            <td>
                                <strong><?php echo $service['username']; ?></strong><br>
                                <small><?php echo $service['email']; ?></small>
                            </td>
                            <td><?php echo ucfirst($service['service_type']); ?></td>
                            <td><?php echo $service['description']; ?></td>
                            <td class="urgency-<?php echo $service['urgency']; ?>">
                                <?php echo ucfirst($service['urgency']); ?>
                            </td>
                            <td class="status-<?php echo $service['status']; ?>">
                                <?php echo ucfirst($service['status']); ?>
                            </td>
                            <td><?php echo $service['created_at']; ?></td>
                            <td>
                                <form method="POST" class="status-form">
                                    <input type="hidden" name="service_id" value="<?php echo $service['id']; ?>">
                                    <select name="status" required>
                                        <option value="pending" <?php echo $service['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in_progress" <?php echo $service['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="completed" <?php echo $service['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No service requests found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>