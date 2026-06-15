<?php
session_start();
echo "<h1>Admin Debug Info</h1>";

// Check session variables
echo "<h2>Session Variables:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Check database admin users
$conn = new mysqli('localhost', 'root', '', 'saloneride_db');
$result = $conn->query("SELECT id, full_name, phone, user_type, role FROM users WHERE role = 'admin' OR user_type = 'admin'");

echo "<h2>Admin Users in Database:</h2>";
if ($result->num_rows > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Phone</th><th>User Type</th><th>Role</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['full_name'] . "</td>";
        echo "<td>" . $row['phone'] . "</td>";
        echo "<td>" . $row['user_type'] . "</td>";
        echo "<td>" . ($row['role'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color:red'>No admin users found! Register using admin code: SIERRA2026</p>";
}

$conn->close();
?>