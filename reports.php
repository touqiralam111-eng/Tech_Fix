<?php
include 'check_login.php';
$user = check_login();

// Check if user is admin
if ($user['user_type'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - Admin Panel</title>
    <link rel="stylesheet" href="cssstyle.css">
    <style>
        .report-section {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .report-filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .report-filters input, .report-filters select {
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .report-table th, .report-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        .report-table th {
            background: #35424a;
            color: white;
        }
        
        .chart-container {
            background: white;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            height: 400px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reports & Analytics</h2>
        <div>
            <a href="admin_dashboard.php" style="color: white; margin-right: 15px;">Dashboard</a>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="report-filters">
            <select id="reportType">
                <option value="user_activity">User Activity</option>
                <option value="service_requests">Service Requests</option>
                <option value="login_logs">Login Reports</option>
            </select>
            
            <input type="date" id="startDate">
            <input type="date" id="endDate">
            
            <button onclick="loadReport()">Generate Report</button>
            <button onclick="exportReport()">Export PDF</button>
        </div>

        <!-- User Activity Report -->
        <div class="report-section" id="userActivityReport">
            <h3>User Activity Report</h3>
            <div class="chart-container" id="userChart">
                <p>User registration chart will appear here</p>
            </div>
            
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>User Type</th>
                        <th>Registration Date</th>
                        <th>Last Login</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $users_sql = "SELECT u.*, MAX(l.login_time) as last_login 
                                 FROM users u 
                                 LEFT JOIN login_logs l ON u.id = l.user_id 
                                 GROUP BY u.id 
                                 ORDER BY u.created_at DESC";
                    $users_result = $conn->query($users_sql);
                    
                    while ($user_data = $users_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$user_data['username']}</td>
                                <td>{$user_data['email']}</td>
                                <td>{$user_data['user_type']}</td>
                                <td>{$user_data['created_at']}</td>
                                <td>" . ($user_data['last_login'] ? $user_data['last_login'] : 'Never') . "</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Service Requests Report -->
        <div class="report-section" id="serviceRequestsReport" style="display: none;">
            <h3>Service Requests Report</h3>
            <div class="chart-container" id="serviceChart">
                <p>Service requests chart will appear here</p>
            </div>
            
            <table class="report-table">
                <thead>
                    <tr>
                        <th>Request ID</th>
                        <th>User</th>
                        <th>Service Type</th>
                        <th>Urgency</th>
                        <th>Status</th>
                        <th>Created Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $services_sql = "SELECT sr.*, u.username 
                                    FROM service_requests sr 
                                    JOIN users u ON sr.user_id = u.id 
                                    ORDER BY sr.created_at DESC";
                    $services_result = $conn->query($services_sql);
                    
                    while ($service = $services_result->fetch_assoc()) {
                        echo "<tr>
                                <td>#{$service['id']}</td>
                                <td>{$service['username']}</td>
                                <td>" . ucfirst($service['service_type']) . "</td>
                                <td>" . ucfirst($service['urgency']) . "</td>
                                <td class='status-{$service['status']}'>" . ucfirst($service['status']) . "</td>
                                <td>{$service['created_at']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Login Logs Report -->
        <div class="report-section" id="loginLogsReport" style="display: none;">
            <h3>Login Activity Report</h3>
            <table class="report-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Login Time</th>
                        <th>IP Address</th>
                        <th>Session Duration</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $logs_sql = "SELECT u.username, l.login_time, l.ip_address 
                                FROM login_logs l 
                                JOIN users u ON l.user_id = u.id 
                                ORDER BY l.login_time DESC 
                                LIMIT 50";
                    $logs_result = $conn->query($logs_sql);
                    
                    while ($log = $logs_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$log['username']}</td>
                                <td>{$log['login_time']}</td>
                                <td>{$log['ip_address']}</td>
                                <td>N/A</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function loadReport() {
            const reportType = document.getElementById('reportType').value;
            
            // Hide all reports
            document.getElementById('userActivityReport').style.display = 'none';
            document.getElementById('serviceRequestsReport').style.display = 'none';
            document.getElementById('loginLogsReport').style.display = 'none';
            
            // Show selected report
            document.getElementById(reportType + 'Report').style.display = 'block';
        }
        
        function exportReport() {
            alert('PDF export functionality would be implemented here');
            // In a real application, this would generate a PDF report
        }
        
        // Load default report
        loadReport();
    </script>
</body>
</html>