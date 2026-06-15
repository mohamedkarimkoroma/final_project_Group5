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

$user_id = $_SESSION['user_id'];

// Get the most recent active trip
$result = $conn->query("SELECT * FROM trips WHERE passenger_id = $user_id AND status = 'active' ORDER BY departure_time DESC LIMIT 1");

if ($result->num_rows > 0) {
    $trip = $result->fetch_assoc();
    echo json_encode(['success' => true, 'trip' => $trip]);
} else {
    echo json_encode(['success' => false, 'message' => 'No active trips found']);
}

$conn->close();
?>