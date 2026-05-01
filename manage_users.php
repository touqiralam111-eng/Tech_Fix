<?php
include 'check_login.php';
$user = check_login();

// Check if user is admin
if ($user['user_type'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    if ($_GET['action'] == 'delete' && $user_id != $user['user_id']) {
        // Don't allow deleting yourself
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = "User deleted successfully";
        } else {
            $error = "Error deleting user: " . $stmt->error;
        }
    } elseif ($_GET['action'] == 'toggle_status') {
        // Toggle user status (active/inactive)
        $toggle_sql = "UPDATE users SET user_type = CASE WHEN user_type = 'admin' THEN 'user' ELSE 'admin' END WHERE id = ?";
        $stmt = $conn->prepare($toggle_sql);
        $stmt->bind_param("i", $user_id);
        
        if ($stmt->execute()) {
            $message = "User status updated successfully";
        } else {
            $error = "Error updating user: " . $stmt->error;
        }
    }
}

// Get all users
$users_sql = "SELECT u.*, COUNT(sr.id) as service_count, MAX(l.login_time) as last_login 
             FROM users u 
             LEFT JOIN service_requests sr ON u.id = sr.user_id 
             LEFT JOIN login_logs l ON u.id = l.user_id 
             GROUP BY u.id 
             ORDER BY u.created_at DESC";
$users_result = $conn->query($users_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Users - Admin Panel</title>
    <link rel="stylesheet" href="cssstyle.css">
    <style>
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .user-table th, .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .user-table th {
            background: #35424a;
            color: white;
        }
        
        .action-btns {
            display: flex;
            gap: 5px;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }
        
        .btn-edit { background: #2196F3; color: white; }
        .btn-delete { background: #f44336; color: white; }
        .btn-toggle { background: #FF9800; color: white; }
        
        .admin-badge { background: #4CAF50; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; }
        .user-badge { background: #2196F3; color: white; padding: 3px 8px; border-radius: 12px; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Manage Users</h2>
        <div>
            <a href="admin_dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
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

        <div class="admin-stats">
            <h3>Users Summary</h3>
            <?php
            $total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
            $total_admins = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type='admin'")->fetch_assoc()['count'];
            $total_services = $conn->query("SELECT COUNT(*) as count FROM service_requests")->fetch_assoc()['count'];
            ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h4>Total Users</h4>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Administrators</h4>
                    <p><?php echo $total_admins; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Regular Users</h4>
                    <p><?php echo $total_users - $total_admins; ?></p>
                </div>
                <div class="stat-card">
                    <h4>Total Services</h4>
                    <p><?php echo $total_services; ?></p>
                </div>
            </div>
        </div>

        <div class="users-list">
            <h3>All Users</h3>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Services</th>
                        <th>Last Login</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user_data = $users_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $user_data['id']; ?></td>
                        <td>
                            <?php echo $user_data['username']; ?>
                            <?php if ($user_data['id'] == $user['user_id']): ?>
                                <span style="color: #666;">(You)</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user_data['email']; ?></td>
                        <td>
                            <?php if ($user_data['user_type'] == 'admin'): ?>
                                <span class="admin-badge">Admin</span>
                            <?php else: ?>
                                <span class="user-badge">User</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $user_data['service_count']; ?></td>
                        <td><?php echo $user_data['last_login'] ? $user_data['last_login'] : 'Never'; ?></td>
                        <td><?php echo $user_data['created_at']; ?></td>
                        <td class="action-btns">
                            <?php if ($user_data['id'] != $user['user_id']): ?>
                                <a href="?action=toggle_status&id=<?php echo $user_data['id']; ?>" class="btn btn-toggle">
                                    Make <?php echo $user_data['user_type'] == 'admin' ? 'User' : 'Admin'; ?>
                                </a>
                                <a href="?action=delete&id=<?php echo $user_data['id']; ?>" class="btn btn-delete" 
                                   onclick="return confirm('Are you sure you want to delete this user?')">
                                    Delete
                                </a>
                            <?php else: ?>
                                <span style="color: #666;">Current User</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>