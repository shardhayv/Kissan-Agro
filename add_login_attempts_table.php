<?php
/**
 * Add Login Attempts Table
 * 
 * This script adds the login_attempts table to the database.
 */

// Include database connection
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('login_attempts.sql');

// Execute the SQL
if (mysqli_multi_query($conn, $sql)) {
    echo "Login attempts table created successfully.<br>";
    echo "<p>Setup complete. <a href='/mill/admin/index.php'>Go to Admin Login</a></p>";
} else {
    echo "Error creating login attempts table: " . mysqli_error($conn);
}
?>
