<?php
/**
 * Add Order Logs Table
 * 
 * This script adds the order_logs table to the database.
 */

// Include database connection
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('order_logs.sql');

// Execute the SQL
if (mysqli_query($conn, $sql)) {
    echo "Order logs table created successfully.<br>";
    echo "<p>Setup complete. <a href='/mill/admin/orders.php'>Go to Order Management</a></p>";
} else {
    echo "Error creating order logs table: " . mysqli_error($conn);
}
?>
