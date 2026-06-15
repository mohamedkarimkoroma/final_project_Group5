<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Police Portal - Salone Safe Ride</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Tahoma; background: #f0f0f0; }
        .header {
            background: #1a237e;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .alert-card {
            background: #ffebee;
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }
        input, select {
            padding: 10px;
            margin: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #1a237e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th { background: #1a237e; color: white; }
        .status-active { color: #dc3545; font-weight: bold; }
        .status-resolved { color: green; }
    </style>
</head>
<body>
    <div class="header">
        <h1>👮 Sierra Leone Police - Emergency Response Portal</h1>
        <p>Salone Safe Ride | Real-time Accident & Manifest Access</p>
    </div>

    <div class="container">
        <div class="card">
            <h2>🚨 Active SOS Alerts</h2>
            <?php
            $alerts = $conn->query("SELECT * FROM sos_alerts WHERE status = 'active' ORDER BY alert_time DESC");
            if ($alerts->num_rows > 0) {
                while ($alert = $alerts->fetch_assoc()) {
                    echo "<div class='alert-card'>";
                    echo "<strong>🚨 EMERGENCY ALERT</strong><br>";
                    echo "Passenger: " . $alert['passenger_name'] . "<br>";
                    echo "Phone: " . $alert['passenger_phone'] . "<br>";
                    echo "Time: " . $alert['alert_time'] . "<br>";
                    echo "Location: " . ($alert['location'] ?: 'Unknown') . "<br>";
                    echo "<button onclick=\"resolveAlert(" . $alert['id'] . ")\">Mark as Responded</button>";
                    echo "</div>";
                }
            } else {
                echo "<p>✅ No active SOS alerts at this time.</p>";
            }
            ?>
        </div>

        <div class="card">
            <h2>🔍 Search Passenger Manifest</h2>
            <input type="text" id="searchPhone" placeholder="Search by Phone Number">
            <input type="text" id="searchVehicle" placeholder="Search by Vehicle Registration">
            <button onclick="searchManifest()">Search</button>
            <div id="searchResults"></div>
        </div>

        <div class="card">
            <h2>📋 Recent Accident Reports</h2>
            <table>
                <tr>
                    <th>Time</th>
                    <th>Passenger</th>
                    <th>Vehicle</th>
                    <th>Status</th>
                </tr>
                <?php
                $recent = $conn->query("SELECT * FROM sos_alerts ORDER BY alert_time DESC LIMIT 10");
                while ($row = $recent->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['alert_time'] . "</td>";
                    echo "<td>" . $row['passenger_name'] . "</td>";
                    echo "<td>" . ($row['trip_id'] ? 'Trip #' . $row['trip_id'] : 'N/A') . "</td>";
                    echo "<td class='status-active'>" . $row['status'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </table>
        </div>
    </div>

    <script>
        function searchManifest() {
            const phone = document.getElementById('searchPhone').value;
            const vehicle = document.getElementById('searchVehicle').value;
            
            fetch(`search_manifest.php?phone=${phone}&vehicle=${vehicle}`)
                .then(res => res.json())
                .then(data => {
                    let html = '<h3>Search Results:</h3>';
                    data.forEach(trip => {
                        html += `<div style="border:1px solid #ddd; padding:10px; margin:10px;">
                                    <strong>${trip.origin} → ${trip.destination}</strong><br>
                                    Passenger: ${trip.passenger_name}<br>
                                    Vehicle: ${trip.vehicle_reg}<br>
                                    Emergency Contact: ${trip.emergency_name} (${trip.emergency_phone})
                                </div>`;
                    });
                    document.getElementById('searchResults').innerHTML = html;
                });
        }
        
        function resolveAlert(id) {
            fetch(`resolve_alert.php?id=${id}`)
                .then(() => location.reload());
        }
    </script>
</body>
</html>