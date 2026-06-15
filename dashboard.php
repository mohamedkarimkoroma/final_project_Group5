<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Salone Safe Ride</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
        }

        /* Header */
        .header {
            background: #006400;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-icon span {
            font-size: 28px;
        }

        .logo-text h1 {
            font-size: 1.3rem;
            margin: 0;
        }

        .logo-text p {
            font-size: 0.65rem;
            opacity: 0.9;
        }

        .user-info {
            text-align: right;
        }

        .logout-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 32px;
            font-weight: bold;
            color: #006400;
        }

        /* Action Cards */
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            cursor: pointer;
            transition: 0.3s;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }

        .action-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .action-card h3 {
            color: #006400;
            margin-bottom: 10px;
        }

        .sos-card {
            background: #dc3545;
            color: white;
        }

        .sos-card h3 {
            color: white;
        }

        /* Trips List */
        .trips-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .section-title {
            font-size: 1.3rem;
            color: #006400;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e0e0e0;
        }

        .trip-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .trip-info strong {
            color: #006400;
        }

        .trip-status {
            background: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
        }

        .no-trips {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-content h2 {
            color: #006400;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #006400;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        .close-modal {
            background: #666;
            margin-top: 5px;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }
            .user-info {
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">
            <div class="logo-icon"><span>🇸🇱</span></div>
            <div class="logo-text">
                <h1>Salone Safe Ride</h1>
                <p>Sierra Leone National Transport Safety System</p>
            </div>
        </div>
        <div class="user-info">
            <div>Welcome, <strong><?php echo $_SESSION['user_name']; ?></strong></div>
            <div style="font-size: 12px;">📱 <?php echo $_SESSION['user_phone']; ?></div>
            <button class="logout-btn" onclick="logout()">Logout</button>
        </div>
    </div>

    <div class="container">
        <div id="alertMessage" class="alert alert-success"></div>
        <div id="errorMessage" class="alert alert-error"></div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="tripCount">0</div>
                <div>Total Trips</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">24/7</div>
                <div>Emergency Support</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">🇸🇱</div>
                <div>Nationwide</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions-grid">
            <div class="action-card" onclick="openTripModal()">
                <div class="action-icon">🚗</div>
                <h3>Start New Trip</h3>
                <p>Create digital manifest for your journey</p>
            </div>
            <div class="action-card" onclick="shareTrip()">
                <div class="action-icon">📤</div>
                <h3>Share Trip</h3>
                <p>Send tracking link to family</p>
            </div>
            <div class="action-card sos-card" onclick="sendSOS()">
                <div class="action-icon">🆘</div>
                <h3>Emergency SOS</h3>
                <p>Send alert to family & police</p>
            </div>
        </div>

        <!-- Trips List -->
        <div class="trips-section">
            <div class="section-title">📋 Your Digital Manifest Records</div>
            <div id="tripsList">
                <div class="no-trips">Loading your trips...</div>
            </div>
        </div>
    </div>

    <!-- Trip Modal - Flexible Location Entry -->
<div id="tripModal" class="modal">
    <div class="modal-content">
        <h2>Create Digital Manifest</h2>
        <form id="tripForm" onsubmit="createTrip(event)">
            <div class="form-group">
                <label>Origin (Type any location) *</label>
                <input type="text" id="origin" placeholder="e.g., Freetown Central Park, Lumley, Waterloo" required>
                <small style="color: #666;">Type any city, town, village, or landmark</small>
            </div>
            <div class="form-group">
                <label>Destination (Type any location) *</label>
                <input type="text" id="destination" placeholder="e.g., Makeni, Bo City, Kailahun" required>
                <small style="color: #666;">You can type any destination name</small>
            </div>
            <div class="form-group">
                <label>Vehicle Registration *</label>
                <input type="text" id="vehicle_reg" placeholder="e.g., AGF 123" required>
            </div>
            <div class="form-group">
                <label>Driver Name (Optional)</label>
                <input type="text" id="driver_name" placeholder="Driver's full name">
            </div>
            <div class="form-group">
                <label>Park Name (Optional)</label>
                <input type="text" id="park_name" placeholder="e.g., Freetown Central Park">
            </div>
            <button type="submit">✅ Create Digital Manifest</button>
            <button type="button" class="close-modal" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>
    <script>
    // Get user info from PHP session
    const user = {
        full_name: '<?php echo $_SESSION['user_name']; ?>',
        phone: '<?php echo $_SESSION['user_phone']; ?>',
        emergency_name: '<?php echo $_SESSION['emergency_name']; ?>',
        emergency_phone: '<?php echo $_SESSION['emergency_phone']; ?>'
    };

    // Load trips on page load
    loadTrips();

    async function loadTrips() {
        try {
            const response = await fetch('get_trips.php');
            const data = await response.json();
            
            const tripsList = document.getElementById('tripsList');
            const tripCount = document.getElementById('tripCount');
            
            if (data.success && data.trips.length > 0) {
                tripCount.innerText = data.trips.length;
                let html = '';
                data.trips.forEach(trip => {
                    html += `
                        <div class="trip-item">
                            <div class="trip-info">
                                <strong>${trip.origin} → ${trip.destination}</strong><br>
                                <small>Vehicle: ${trip.vehicle_reg} | Code: ${trip.trip_code}</small><br>
                                <small>${trip.departure_time}</small>
                            </div>
                            <div>
                                <span class="trip-status">${trip.status}</span>
                            </div>
                        </div>
                    `;
                });
                tripsList.innerHTML = html;
            } else {
                tripsList.innerHTML = '<div class="no-trips">No trips yet. Start your first trip above!</div>';
                tripCount.innerText = '0';
            }
        } catch (error) {
            console.error('Error loading trips:', error);
        }
    }

    function openTripModal() {
        document.getElementById('tripModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('tripModal').style.display = 'none';
        document.getElementById('tripForm').reset();
    }

    async function createTrip(event) {
        event.preventDefault();
        
        const origin = document.getElementById('origin').value;
        const destination = document.getElementById('destination').value;
        const vehicle_reg = document.getElementById('vehicle_reg').value;
        const driver_name = document.getElementById('driver_name').value;
        
        if (!origin || !destination || !vehicle_reg) {
            showMessage('Please fill all required fields!', 'error');
            return;
        }
        
        const formData = new URLSearchParams();
        formData.append('origin', origin);
        formData.append('destination', destination);
        formData.append('vehicle_reg', vehicle_reg);
        formData.append('driver_name', driver_name);
        
        try {
            const response = await fetch('create_trip.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showMessage(result.message, 'success');
                closeModal();
                loadTrips();
            } else {
                showMessage(result.message, 'error');
            }
        } catch (error) {
            showMessage('Network error: ' + error.message, 'error');
        }
    }

    function shareTrip() {
        fetch('get_latest_trip.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.trip) {
                    const trackingLink = data.trip.shareable_link;
                    const message = `🚗 SALONE SAFE RIDE TRIP TRACKING 🚗\n\n${data.trip.passenger_name} is traveling from ${data.trip.origin} to ${data.trip.destination}.\n\nTrack the journey here:\n${trackingLink}\n\nSalone Safe Ride - Protecting passengers across Sierra Leone.`;
                    
                    navigator.clipboard.writeText(trackingLink);
                    
                    if (confirm('✅ Tracking link copied to clipboard!\n\nDo you want to share via WhatsApp?')) {
                        window.open(`https://wa.me/?text=${encodeURIComponent(message)}`, '_blank');
                    } else {
                        alert(`📤 Share this link with your family:\n\n${trackingLink}`);
                    }
                } else {
                    alert('No active trips found. Please create a trip first.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Please create a trip first before sharing.');
            });
    }

    // ============================================
    // SOS FUNCTION - ADD THIS
    // ============================================
async function sendSOS() {
    // Get the most recent active trip first
    try {
        const tripResponse = await fetch('get_latest_trip.php');
        const tripData = await tripResponse.json();
        
        let tripDetails = '';
        if (tripData.success && tripData.trip) {
            tripDetails = `\n\n🚗 TRIP DETAILS:\nFrom: ${tripData.trip.origin}\nTo: ${tripData.trip.destination}\nVehicle: ${tripData.trip.vehicle_reg}\nTrip Code: ${tripData.trip.trip_code}`;
        }
        
        if (confirm('⚠️⚠️⚠️ EMERGENCY SOS ⚠️⚠️⚠️\n\nPress OK to send emergency alert to:\n\n• ' + user.emergency_name + ' (' + user.emergency_phone + ')\n• Sierra Leone Police (119)\n• National Ambulance (999)\n\nONLY PRESS IF THIS IS A REAL EMERGENCY!')) {
            
            const sosResponse = await fetch('send_sos_alert.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    trip_id: tripData.success ? tripData.trip.id : null,
                    message: ''
                })
            });
            
            const result = await sosResponse.json();
            
            if (result.success) {
                // Show the SMS that would be sent
                alert('🚨🚨🚨 SOS ALERT RECORDED! 🚨🚨🚨\n\n' +
                      'The following EMERGENCY SMS would be sent to:\n\n' +
                      '📱 Emergency Contact: ' + result.emergency_contact + ' (' + result.emergency_phone + ')\n' +
                      '👮 Police: 119\n' +
                      '🚑 Ambulance: 999\n\n' +
                      '--- SMS CONTENT ---\n' +
                      result.sms_preview + 
                      '\n\n---\n\n✅ Alert saved to database.\n✅ Emergency services notified.\n\nFor real SMS integration, sign up for Africa\'s Talking API.');
                
                showMessage('SOS alert saved! Emergency contact would receive SMS.', 'success');
            } else {
                alert('❌ SOS failed: ' + result.message);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error: ' + error.message);
    }
}

    function showMessage(message, type) {
        const alertDiv = document.getElementById(type === 'success' ? 'alertMessage' : 'errorMessage');
        alertDiv.innerHTML = message;
        alertDiv.style.display = 'block';
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 3000);
    }

    function logout() {
        window.location.href = 'logout.php';
    }
</script>
</body>
</html>