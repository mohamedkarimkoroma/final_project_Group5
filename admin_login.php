<?php
session_start();

/*
 * Salone Safe Ride - Sierra Leone National Transport Safety System
 * Copyright (c) 2026 [Your Name]
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 * 
 * For Sierra Leone - Protecting passengers across all 16 districts.
 */
// If already logged in as admin, go to admin dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: admin_dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    
    $conn = new mysqli('localhost', 'root', '', 'saloneride_db');
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Find user by phone
    $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Check if user is admin
        if ($user['role'] !== 'admin' && $user['user_type'] !== 'admin') {
            $error = "This account is not an admin account!";
        } else {
            // Verify password
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_type'] = $user['user_type'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['is_admin'] = true;
                
                header('Location: admin_dashboard.php');
                exit;
            } else {
                $error = "Incorrect password!";
            }
        }
    } else {
        $error = "Phone number not found!";
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login - Salone Safe Ride</title>
    <style>
        body {
            font-family: Tahoma;
            background: linear-gradient(135deg, #006400, #008000);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
            padding: 20px;
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 15px;
            width: 400px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        h1 { color: #006400; margin-bottom: 10px; }
        .subtitle { color: #666; margin-bottom: 20px; font-size: 14px; }
        .admin-badge {
            background: #dc3545;
            color: white;
            padding: 8px;
            border-radius: 20px;
            font-size: 12px;
            margin-bottom: 20px;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
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
        .error {
            color: #721c24;
            background: #f8d7da;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info {
            background: #e8f5e9;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 12px;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🇸🇱 Admin Login</h1>
        <div class="subtitle">Salone Safe Ride - System Administration</div>
        <div class="admin-badge">🔐 RESTRICTED ACCESS 🔐</div>
        
        <?php if ($error): ?>
            <div class="error">❌ <?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="tel" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">🔑 Login as Admin</button>
        </form>
        
        <div class="info">
            <strong>📋 How to become Admin:</strong><br>
            1. Go to <a href="register.html">Registration Page</a><br>
            2. Enter the secret admin code: <code>SIERRA2026</code><br>
            3. Complete registration<br>
            4. Login here with your phone and password
        </div>
        
        <div style="margin-top: 15px;">
            <a href="index.html">← Back to Home</a> | 
            <a href="login.html">User Login</a>
        </div>
    </div>
</body>
</html>