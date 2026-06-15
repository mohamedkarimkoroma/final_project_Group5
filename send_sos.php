<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

$passenger_id = $_SESSION['user_id'];
$passenger_name = $_SESSION['user_name'];
$passenger_phone = $_SESSION['user_phone'];
$emergency_name = $_SESSION['emergency_name'];
$emergency_phone = $_SESSION['emergency_phone'];
$location = 'Current location - ' . date('Y-m-d H:i:s');

// Insert SOS alert
$sql = "INSERT INTO sos_alerts (passenger_id, passenger_name, passenger_phone, location, status) 
        VALUES ('$passenger_id', '$passenger_name', '$passenger_phone', '$location', 'active')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true, 
        'message' => 'SOS ALERT SENT! Your emergency contact (' . $emergency_name . ' at ' . $emergency_phone . ') has been notified. Police (119) and hospitals have been alerted.'
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to send SOS: ' . $conn->error
    ]);
}

$conn->close();
?>