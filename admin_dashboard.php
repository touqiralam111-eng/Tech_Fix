<?php
// admin_dashboard.php - Fixed Version
include 'config.php';

if (!is_logged_in()) {
    redirect('login.php');
}

if (!is_admin()) {
    // If not admin but logged in, redirect to user dashboard
    redirect('dashboard.php');
}

// Safe statistics queries
$total_visits = get_visit_count();
$total_users = get_user_count();

$total_services = 0;
$pending_services = 0;
$completed_services = 0;
$revenue = 0;

$services_result = $conn->query("SELECT COUNT(*) as count FROM service_requests");
if ($services_result) {
    $total_services = $services_result->fetch_assoc()['count'];
}

$pending_result = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='pending'");
if ($pending_result) {
    $pending_services = $pending_result->fetch_assoc()['count'];
}

$completed_result = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='completed'");
if ($completed_result) {
    $completed_services = $completed_result->fetch_assoc()['count'];
}

$revenue_result = $conn->query("SELECT SUM(budget) as total FROM service_requests WHERE status='completed'");
if ($revenue_result) {
    $revenue_data = $revenue_result->fetch_assoc();
    $revenue = $revenue_data['total'] ?? 0;
}

// Get recent activities safely
$recent_activities = $conn->query("
    SELECT 'user_registration' as type, username, created_at FROM users 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    UNION ALL
    SELECT 'service_request' as type, CONCAT('Request #', id) as username, created_at FROM service_requests 
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY created_at DESC 
    LIMIT 10
") ?: [];

// Get recent users safely
$recent_users = $conn->query("SELECT username, email, created_at FROM users ORDER BY created_at DESC LIMIT 5") ?: [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TechFix | Professional IT Solutions</title>
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
                    <li><a href="admin_dashboard.php" class="active"><i class="fas fa-cog"></i> Admin</a></li>
                    <li><a href="manage_users.php"><i class="fas fa-users"></i> Users</a></li>
                    <li><a href="manage_services.php"><i class="fas fa-tools"></i> Services</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Reports</a></li>
                </ul>
                <div class="user-menu">
                    <div class="user-info">
                        <div class="user-avatar" style="background: linear-gradient(135deg, var(--danger), #b5179e);">
                            <i class="fas fa-crown"></i>
                        </div>
                        <span>Admin: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </div>
                    <a href="logout.php" class="btn btn-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="container">
            <!-- Welcome Section -->
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
                    <p>Welcome to the TechFix administration panel</p>
                </div>
                
                <!-- Key Metrics -->
                <div class="dashboard-stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_visits; ?></div>
                        <div class="stat-label">Total Visits</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_users; ?></div>
                        <div class="stat-label">Registered Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_services; ?></div>
                        <div class="stat-label">Service Requests</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number">₹<?php echo number_format($revenue, 2); ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                <!-- Main Content -->
                <div>
                    <!-- Recent Activities -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-history"></i> Recent Activities</h2>
                        </div>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Activity</th>
                                        <th>User/Request</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($recent_activities && $recent_activities->num_rows > 0): ?>
                                        <?php while ($activity = $recent_activities->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <span class="badge <?php echo $activity['type'] == 'user_registration' ? 'badge-success' : 'badge-primary'; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $activity['type'])); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($activity['username']); ?></td>
                                            <td><?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?></td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3" style="text-align: center; color: var(--gray);">No recent activities</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Service Requests Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-tasks"></i> Service Requests Overview</h2>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; text-align: center;">
                            <div style="background: #fff3cd; padding: 1.5rem; border-radius: var(--radius);">
                                <div style="font-size: 2rem; font-weight: bold; color: #856404;"><?php echo $pending_services; ?></div>
                                <div style="color: #856404;">Pending</div>
                            </div>
                            <div style="background: #cce7ff; padding: 1.5rem; border-radius: var(--radius);">
                                <div style="font-size: 2rem; font-weight: bold; color: #004085;">
                                    <?php 
                                    $in_progress = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='in_progress'");
                                    echo $in_progress ? $in_progress->fetch_assoc()['count'] : 0;
                                    ?>
                                </div>
                                <div style="color: #004085;">In Progress</div>
                            </div>
                            <div style="background: #d4edda; padding: 1.5rem; border-radius: var(--radius);">
                                <div style="font-size: 2rem; font-weight: bold; color: #155724;"><?php echo $completed_services; ?></div>
                                <div style="color: #155724;">Completed</div>
                            </div>
                            <div style="background: #f8d7da; padding: 1.5rem; border-radius: var(--radius);">
                                <div style="font-size: 2rem; font-weight: bold; color: #721c24;">
                                    <?php 
                                    $cancelled = $conn->query("SELECT COUNT(*) as count FROM service_requests WHERE status='cancelled'");
                                    echo $cancelled ? $cancelled->fetch_assoc()['count'] : 0;
                                    ?>
                                </div>
                                <div style="color: #721c24;">Cancelled</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div>
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-bolt"></i> Quick Actions</h2>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <a href="manage_users.php" class="btn btn-primary" style="text-align: left; justify-content: start;">
                                <i class="fas fa-users"></i> Manage Users
                            </a>
                            <a href="manage_services.php" class="btn btn-success" style="text-align: left; justify-content: start;">
                                <i class="fas fa-tools"></i> Manage Services
                            </a>
                            <a href="reports.php" class="btn btn-info" style="text-align: left; justify-content: start;">
                                <i class="fas fa-chart-bar"></i> View Reports
                            </a>
                        </div>
                    </div>

                    <!-- System Status -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-server"></i> System Status</h2>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <span>Website Status</span>
                                <span class="badge badge-completed" style="background: var(--success);">Online</span>
                            </div>
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <span>Database</span>
                                <span class="badge badge-completed" style="background: var(--success);">Connected</span>
                            </div>
                            <div style="display: flex; justify-content: between; align-items: center;">
                                <span>Pending Requests</span>
                                <span class="badge badge-pending"><?php echo $pending_services; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Users -->
                    <div class="card">
                        <div class="card-header">
                            <h2 class="card-title"><i class="fas fa-user-plus"></i> Recent Users</h2>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            <?php if ($recent_users && $recent_users->num_rows > 0): ?>
                                <?php while ($user = $recent_users->fetch_assoc()): ?>
                                <div style="display: flex; align-items: center; gap: 1rem; padding: 0.75rem; background: var(--light); border-radius: var(--radius);">
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                        <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($user['username']); ?></div>
                                        <div style="font-size: 0.8rem; color: var(--gray);"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </div>
                                    <div style="font-size: 0.8rem; color: var(--gray);">
                                        <?php echo date('M j', strtotime($user['created_at'])); ?>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div style="text-align: center; color: var(--gray); padding: 1rem;">No users found</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 TechFix. All rights reserved. | Admin Dashboard</p>
        </div>
    </footer>

    <script src="jsscript.js"></script>
</body>
</html>