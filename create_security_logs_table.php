<?php
// Include database connection
require_once 'config/database.php';

// Create security_logs table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS security_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_type VARCHAR(50) NOT NULL,
    description TEXT NOT NULL,
    user_id INT DEFAULT 0,
    username VARCHAR(50) DEFAULT 'guest',
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(255),
    request_uri VARCHAR(255),
    additional_data TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX (event_type),
    INDEX (user_id),
    INDEX (ip_address),
    INDEX (created_at)
)";

if (mysqli_query($conn, $query)) {
    echo "Table 'security_logs' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

echo "Done!";
?>
