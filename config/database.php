<?php

/**
 * Database Configuration
 *
 * This file contains the database connection settings for the Kissan Agro Foods website.
 * It uses environment.php to determine whether to use development or production settings.
 */

// Include environment configuration if not already included
if (!defined('ENV_DEVELOPMENT')) {
    require_once __DIR__ . '/environment.php';
}

// Database credentials are now defined in environment.php

// Create database connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset to ensure proper handling of special characters
mysqli_set_charset($conn, "utf8mb4");
