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

$result = $conn->query("SELECT * FROM trips WHERE passenger_id = $user_id ORDER BY departure_time DESC");

$trips = [];
while ($row = $result->fetch_assoc()) {
    $trips[] = $row;
}

echo json_encode(['success' => true, 'trips' => $trips]);

$conn->close();
?>