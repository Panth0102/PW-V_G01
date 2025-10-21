<?php
/**
 * Simple Login Test Script
 * Use this to debug login issues
 */

// Start session
session_start();

// Include database connection
require_once 'config/connect.php';

echo "<h2>SkillSwap Login Test</h2>";

// Test database connection
if ($conn->connect_error) {
    echo "<p style='color: red;'>Database connection failed: " . $conn->connect_error . "</p>";
    exit;
} else {
    echo "<p style='color: green;'>Database connection successful!</p>";
}

// Test user query
$test_email = 'admin@skillswap.com';
$sql = "SELECT User_ID, Name, Email, Password FROM Users WHERE Email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $test_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<p style='color: green;'>User found!</p>";
    echo "<p>User ID: " . $row['User_ID'] . "</p>";
    echo "<p>Name: " . htmlspecialchars($row['Name']) . "</p>";
    echo "<p>Email: " . htmlspecialchars($row['Email']) . "</p>";
    echo "<p>Password: " . htmlspecialchars($row['Password']) . "</p>";
    
    // Test login with correct password
    $test_password = 'admin123';
    if ($test_password === $row['Password']) {
        echo "<p style='color: green;'>Password match successful!</p>";
        
        // Test session setting
        $_SESSION['user_id'] = $row['User_ID'];
        $_SESSION['user_name'] = $row['Name'];
        $_SESSION['login_time'] = time();
        
        echo "<p style='color: green;'>Session variables set:</p>";
        echo "<p>Session user_id: " . $_SESSION['user_id'] . "</p>";
        echo "<p>Session user_name: " . $_SESSION['user_name'] . "</p>";
        echo "<p>Session login_time: " . $_SESSION['login_time'] . "</p>";
        
        echo "<p><a href='dashboard.php'>Go to Dashboard</a></p>";
    } else {
        echo "<p style='color: red;'>Password mismatch!</p>";
        echo "<p>Expected: admin123</p>";
        echo "<p>Got: " . htmlspecialchars($row['Password']) . "</p>";
    }
} else {
    echo "<p style='color: red;'>User not found!</p>";
    
    // Show all users
    $all_users_sql = "SELECT User_ID, Name, Email, Password FROM Users";
    $all_result = $conn->query($all_users_sql);
    
    if ($all_result->num_rows > 0) {
        echo "<h3>All Users in Database:</h3>";
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Password</th></tr>";
        while ($user = $all_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $user['User_ID'] . "</td>";
            echo "<td>" . htmlspecialchars($user['Name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['Password']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
}

$stmt->close();
$conn->close();
?>