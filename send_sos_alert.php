<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database error']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$trip_id = $data['trip_id'] ?? null;
$alert_message = $data['message'] ?? '';

$passenger_id = $_SESSION['user_id'];
$passenger_name = $_SESSION['user_name'];
$passenger_phone = $_SESSION['user_phone'];
$emergency_name = $_SESSION['emergency_name'];
$emergency_phone = $_SESSION['emergency_phone'];

$location = 'Freetown - ' . date('Y-m-d H:i:s');

// Insert SOS alert into database
$sql = "INSERT INTO sos_alerts (trip_id, passenger_id, passenger_name, passenger_phone, location, status) 
        VALUES ('$trip_id', '$passenger_id', '$passenger_name', '$passenger_phone', '$location', 'active')";

if ($conn->query($sql) === TRUE) {
    // Create the SMS message that WOULD be sent
    $sms_content = "🚨 SALONE SAFE RIDE EMERGENCY 🚨\n\n";
    $sms_content .= "EMERGENCY ALERT!\n";
    $sms_content .= "Passenger: $passenger_name\n";
    $sms_content .= "Phone: $passenger_phone\n";
    $sms_content .= "Location: $location\n";
    $sms_content .= "Time: " . date('Y-m-d H:i:s') . "\n\n";
    $sms_content .= "Please contact immediately!\n";
    $sms_content .= "Police: 119 | Ambulance: 999";
    
    echo json_encode([
        'success' => true, 
        'message' => '🚨 SOS ALERT SAVED! 🚨',
        'alert_id' => $conn->insert_id,
        'emergency_contact' => $emergency_name,
        'emergency_phone' => $emergency_phone,
        'sms_preview' => $sms_content
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $conn->error
    ]);
}

$conn->close();
?>