<?php
/**
 * Add IP Address to Orders Table
 * 
 * This script adds an IP address column to the orders table.
 */

// Include database connection
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('add_ip_to_orders.sql');

// Execute the SQL
if (mysqli_multi_query($conn, $sql)) {
    echo "IP address column added to orders table successfully.<br>";
    echo "<p>Setup complete. <a href='/mill/admin/orders.php'>Go to Order Management</a></p>";
} else {
    echo "Error adding IP address column: " . mysqli_error($conn);
}
?>
