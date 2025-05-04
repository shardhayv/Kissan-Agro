<?php
/**
 * Add Visitor Logs Table
 * 
 * This script adds the visitor_logs table to the database.
 */

// Include database connection
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('visitor_logs.sql');

// Execute the SQL
if (mysqli_multi_query($conn, $sql)) {
    echo "Visitor logs table created successfully.<br>";
    echo "<p>Setup complete. <a href='/mill/admin/index.php'>Go to Admin Dashboard</a></p>";
} else {
    echo "Error creating visitor logs table: " . mysqli_error($conn);
}
?>
