<?php
/**
 * Test Database Connection
 * 
 * This script tests the database connection with production credentials.
 */

// Force production environment for testing
define('ENV_PRODUCTION', 'production');
$current_environment = ENV_PRODUCTION;

// Set database credentials for production
define('DB_HOST', 'localhost');
define('DB_USER', 'shardhay');
define('DB_PASS', 'Mahakali@5254');
define('DB_NAME', 'shardhay_kissan_agro_foods');

// Attempt to connect to the database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Connection successful! Connected to database: " . DB_NAME;

// Close connection
mysqli_close($conn);
?>
