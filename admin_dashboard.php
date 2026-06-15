<?php
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: admin_login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

// Get statistics
$total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_passengers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'passenger'")->fetch_assoc()['count'];
$total_drivers = $conn->query("SELECT COUNT(*) as count FROM users WHERE user_type = 'driver'")->fetch_assoc()['count'];
$total_trips = $conn->query("SELECT COUNT(*) as count FROM trips")->fetch_assoc()['count'];
$total_sos = $conn->query("SELECT COUNT(*) as count FROM sos_alerts")->fetch_assoc()['count'];
$active_trips = $conn->query("SELECT COUNT(*) as count FROM trips WHERE status = 'active'")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Salone Safe Ride</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
        }
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100%;
            background: #006400;
            color: white;
            padding: 25px;
            overflow-y: auto;
        }
        .sidebar h2 {
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            font-size: 20px;
        }
        .nav-item {
            padding: 12px 15px;
            margin: 5px 0;
            border-radius: 10px;
            cursor: pointer;
            transition: 0.3s;
        }
        .nav-item:hover {
            background: rgba(255,255,255,0.15);
        }
        .nav-item.active {
            background: #ffd700;
            color: #006400;
        }
        .main {
            margin-left: 280px;
            padding: 25px;
        }
        .header {
            background: white;
            padding: 20px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .stat-number {
            font-size: 2rem;
            font-weight: 800;
            color: #006400;
        }
        .stat-label {
            color: #666;
            font-size: 13px;
            margin-top: 5px;
        }
        .card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .card h3 {
            margin-bottom: 15px;
            color: #006400;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
            color: #006400;
            font-weight: 600;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-active { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }
        .logout-btn:hover {
            background: #c82333;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
            }
            .main {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🇸🇱 Salone Safe Ride</h2>
        <div class="nav-item active">📊 Dashboard</div>
        <div class="nav-item">👥 Users</div>
        <div class="nav-item">🚗 Trips</div>
        <div class="nav-item">🚨 SOS Alerts</div>
        <div class="nav-item">🏢 Transport Parks</div>
        <div class="nav-item">📋 Digital Manifests</div>
        <div class="nav-item">⚙️ Settings</div>
    </div>

    <div class="main">
        <div class="header">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
            </div>
            <div>
                <span style="margin-right: 15px;">👑 System Administrator</span>
                <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_passengers; ?></div>
                <div class="stat-label">Passengers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_drivers; ?></div>
                <div class="stat-label">Drivers</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_trips; ?></div>
                <div class="stat-label">Total Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $active_trips; ?></div>
                <div class="stat-label">Active Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_sos; ?></div>
                <div class="stat-label">SOS Alerts</div>
            </div>
        </div>

        <div class="card">
            <h3>📋 Recent Trips (Last 10)</h3>
            <table>
                <thead>
                    <tr>
                        <th>Passenger</th>
                        <th>Origin</th>
                        <th>Destination</th>
                        <th>Vehicle</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_trips = $conn->query("SELECT * FROM trips ORDER BY departure_time DESC LIMIT 10");
                    if ($recent_trips->num_rows > 0) {
                        while ($trip = $recent_trips->fetch_assoc()) {
                            $status_class = $trip['status'] == 'active' ? 'status-active' : 'status-pending';
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($trip['passenger_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($trip['origin']) . "</td>";
                            echo "<td>" . htmlspecialchars($trip['destination']) . "</td>";
                            echo "<td>" . htmlspecialchars($trip['vehicle_reg']) . "</td>";
                            echo "<td><span class='status-badge $status_class'>" . $trip['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>No trips found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>🚨 Recent SOS Alerts</h3>
            <table>
                <thead>
                    <tr>
                        <th>Passenger</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_sos = $conn->query("SELECT * FROM sos_alerts ORDER BY alert_time DESC LIMIT 10");
                    if ($recent_sos->num_rows > 0) {
                        while ($sos = $recent_sos->fetch_assoc()) {
                            $status_class = $sos['status'] == 'active' ? 'status-active' : 'status-pending';
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($sos['passenger_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($sos['passenger_phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($sos['location'] ?? 'Unknown') . "</td>";
                            echo "<td>" . htmlspecialchars($sos['alert_time']) . "</td>";
                            echo "<td><span class='status-badge $status_class'>" . $sos['status'] . "</span></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5' style='text-align: center;'>No SOS alerts</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>👥 Recent Users</h3>
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Type</th>
                        <th>Registered</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $recent_users = $conn->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 10");
                    if ($recent_users->num_rows > 0) {
                        while ($user = $recent_users->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['full_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['user_type']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' style='text-align: center;'>No users found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>