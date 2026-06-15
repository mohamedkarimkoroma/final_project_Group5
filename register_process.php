<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get form data
$full_name = $_POST['full_name'];
$phone = $_POST['phone'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$emergency_name = $_POST['emergency_name'];
$emergency_phone = $_POST['emergency_phone'];
$user_type = $_POST['user_type'];
$district = $_POST['district'];
$town = $_POST['town'];
$admin_code = $_POST['admin_code'] ?? '';

// Check password match
if ($password !== $confirm_password) {
    echo "<script>alert('❌ Passwords do not match!'); window.location.href='register.html';</script>";
    exit;
}

// Check password length
if (strlen($password) < 6) {
    echo "<script>alert('❌ Password must be at least 6 characters!'); window.location.href='register.html';</script>";
    exit;
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if phone already exists
$check = $conn->query("SELECT id FROM users WHERE phone = '$phone'");

if ($check->num_rows > 0) {
    echo "<script>alert('❌ Phone number already registered!'); window.location.href='register.html';</script>";
    exit;
}

// Determine if user becomes admin based on secret code
$secret_admin_code = 'SIERRA2026'; // Change this to any code you want
$is_admin = ($admin_code === $secret_admin_code);
$role = $is_admin ? 'admin' : $user_type;
$user_type_final = $is_admin ? 'admin' : $user_type;

// Insert user
$sql = "INSERT INTO users (full_name, phone, password, emergency_name, emergency_phone, user_type, role, district, town, admin_code) 
        VALUES ('$full_name', '$phone', '$hashed_password', '$emergency_name', '$emergency_phone', '$user_type_final', '$role', '$district', '$town', '$admin_code')";

if ($conn->query($sql) === TRUE) {
    if ($is_admin) {
        echo "<script>alert('✅ ADMIN REGISTRATION SUCCESSFUL! You have been registered as a System Administrator. Please login.'); window.location.href='admin_login.php';</script>";
    } else {
        echo "<script>alert('✅ Registration successful! Welcome to Salone Safe Ride. Please login.'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('❌ Registration failed: " . $conn->error . "'); window.location.href='register.html';</script>";
}

$conn->close();
?>