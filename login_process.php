<?php
session_start();

// Database connection
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$phone = $_POST['phone'];
$password = $_POST['password'];

// Find user
$sql = "SELECT * FROM users WHERE phone = '$phone'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<script>
        alert('❌ Phone number not found!');
        window.location.href = 'login.html';
    </script>";
    exit;
}

$user = $result->fetch_assoc();

if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['emergency_name'] = $user['emergency_name'];
    $_SESSION['emergency_phone'] = $user['emergency_phone'];
    
    echo "<script>
        alert('✅ Welcome back, " . $user['full_name'] . "!');
        window.location.href = 'dashboard.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Incorrect password!');
        window.location.href = 'login.html';
    </script>";
}

$conn->close();
?>