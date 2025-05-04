<?php
// Include database connection
require_once 'config/database.php';

// Create login_attempts table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    locked_until TIMESTAMP NULL DEFAULT NULL,
    INDEX (ip_address),
    INDEX (attempt_time),
    INDEX (locked_until)
)";

if (mysqli_query($conn, $query)) {
    echo "Table 'login_attempts' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

echo "Done!";
?>
