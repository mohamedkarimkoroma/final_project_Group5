<?php
// Get trip code from URL
$trip_code = $_GET['code'] ?? '';

if (empty($trip_code)) {
    die("No trip code provided. Please ask your family member for the correct tracking link.");
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    die("Database connection failed");
}

// Find the trip
$result = $conn->query("SELECT * FROM trips WHERE trip_code = '$trip_code'");

if ($result->num_rows == 0) {
    die("Trip not found. Please check the tracking link.");
}

$trip = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Trip - Salone Safe Ride</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #006400, #008000);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container { max-width: 600px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo-icon {
            background: #006400;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .logo-icon span { font-size: 35px; }
        h1 { color: #006400; text-align: center; margin-bottom: 20px; }
        .trip-details {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label { font-weight: bold; color: #006400; }
        .status {
            background: #d4edda;
            color: #155724;
            padding: 8px 15px;
            border-radius: 20px;
            display: inline-block;
        }
        .emergency-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-top: 20px;
            border-radius: 10px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="logo">
                <div class="logo-icon"><span>🇸🇱</span></div>
                <h1>Salone Safe Ride</h1>
            </div>

            <h2>📍 Trip Tracking</h2>
            <p>You are tracking a trip for <strong><?php echo htmlspecialchars($trip['passenger_name']); ?></strong></p>

            <div class="trip-details">
                <div class="detail-row">
                    <span class="detail-label">Passenger:</span>
                    <span><?php echo htmlspecialchars($trip['passenger_name']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span><?php echo htmlspecialchars($trip['passenger_phone']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">From:</span>
                    <span><?php echo htmlspecialchars($trip['origin']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">To:</span>
                    <span><?php echo htmlspecialchars($trip['destination']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Vehicle:</span>
                    <span><?php echo htmlspecialchars($trip['vehicle_reg']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Driver:</span>
                    <span><?php echo htmlspecialchars($trip['driver_name']) ?: 'Not specified'; ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Departure:</span>
                    <span><?php echo htmlspecialchars($trip['departure_time']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span><span class="status"><?php echo htmlspecialchars($trip['status']); ?></span></span>
                </div>
            </div>

            <div class="emergency-box">
                <strong>🚨 Emergency Information</strong><br>
                If your family member is in danger, call:
                <ul style="margin-top: 10px; margin-left: 20px;">
                    <li>🚓 Sierra Leone Police: <strong>119</strong></li>
                    <li>🚑 National Ambulance: <strong>999</strong></li>
                    <li>🛣️ Road Safety Authority: <strong>112</strong></li>
                </ul>
            </div>

            <div class="footer">
                <p>This is an official Salone Safe Ride tracking page.</p>
                <p>🇸🇱 Together for safer roads in Sierra Leone 🇸🇱</p>
            </div>
        </div>
    </div>
</body>
</html>