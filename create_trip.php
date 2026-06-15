<?php
session_start();
header('Content-Type: application/json');
/**
 * Salone Safe Ride - Sierra Leone National Transport Safety System
 * @license MIT
 * @copyright 2026 Salone Safe Ride
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software to use, copy, modify, merge, publish, and distribute it.
 */

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get form data (allows ANY location text)
$origin = $_POST['origin'];
$destination = $_POST['destination'];
$vehicle_reg = $_POST['vehicle_reg'];
$driver_name = $_POST['driver_name'] ?? '';
$park_name = $_POST['park_name'] ?? '';

$passenger_id = $_SESSION['user_id'];
$passenger_name = $_SESSION['user_name'];
$passenger_phone = $_SESSION['user_phone'];
$emergency_name = $_SESSION['emergency_name'];
$emergency_phone = $_SESSION['emergency_phone'];

$trip_code = 'STR' . time() . rand(100, 999);
$shareable_link = "http://localhost/saloneride/track.php?code=" . $trip_code;

$sql = "INSERT INTO trips (trip_code, passenger_id, passenger_name, passenger_phone, driver_name, vehicle_reg, origin, destination, emergency_name, emergency_phone, shareable_link, status) 
        VALUES ('$trip_code', '$passenger_id', '$passenger_name', '$passenger_phone', '$driver_name', '$vehicle_reg', '$origin', '$destination', '$emergency_name', '$emergency_phone', '$shareable_link', 'active')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true, 
        'message' => 'Digital manifest created! Your trip is registered.',
        'trip_code' => $trip_code,
        'shareable_link' => $shareable_link
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Failed to create trip: ' . $conn->error
    ]);
}

$conn->close();
?>