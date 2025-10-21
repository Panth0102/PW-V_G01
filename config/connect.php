<?php
/**
 * Database Connection File
 * Handles database connections using centralized configuration
 */

require_once __DIR__ . '/config.php';

// Create connection
$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if ($conn->connect_error) {
    if (DEBUG_MODE) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("Database connection failed. Please try again later.");
    }
}

// Set charset to utf8mb4 for proper Unicode support
$conn->set_charset("utf8mb4");

?>
