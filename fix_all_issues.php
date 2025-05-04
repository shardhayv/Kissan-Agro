<?php
// Error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
require_once 'config/database.php';

echo "<h1>Kissan Agro Foods - Fix All Issues</h1>";
echo "<p>This script will fix all database issues and create missing tables.</p>";

// Function to create a table if it doesn't exist
function create_table_if_not_exists($table_name, $create_query)
{
    global $conn;

    // Check if table exists
    $check_query = "SHOW TABLES LIKE '$table_name'";
    $result = mysqli_query($conn, $check_query);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<p>Table '$table_name' already exists.</p>";
        return true;
    } else {
        // Create table
        if (mysqli_query($conn, $create_query)) {
            echo "<p>Table '$table_name' created successfully.</p>";
            return true;
        } else {
            echo "<p>Error creating table '$table_name': " . mysqli_error($conn) . "</p>";
            return false;
        }
    }
}

// 1. Create site_images table
$site_images_query = "CREATE TABLE IF NOT EXISTS site_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_key VARCHAR(50) NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (image_key)
)";

create_table_if_not_exists('site_images', $site_images_query);

// 2. Create security_logs table
$security_logs_query = "CREATE TABLE IF NOT EXISTS security_logs (
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

create_table_if_not_exists('security_logs', $security_logs_query);

// 3. Create login_attempts table
$login_attempts_query = "CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    locked_until TIMESTAMP NULL DEFAULT NULL,
    INDEX (ip_address),
    INDEX (attempt_time),
    INDEX (locked_until)
)";

create_table_if_not_exists('login_attempts', $login_attempts_query);

// 4. Insert default images if site_images table is empty
$check_query = "SELECT COUNT(*) as count FROM site_images";
$result = mysqli_query($conn, $check_query);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    echo "<p>Inserting default images into site_images table...</p>";

    // Insert default images
    $default_images = [
        ['logo', 'logo.png', 'Kissan Agro Foods Logo', 'Main logo of Kissan Agro Foods'],
        ['favicon', 'favicon.ico', 'Favicon', 'Website favicon'],
        ['home_banner', 'home-banner.jpg', 'Home Banner', 'Banner image for the homepage'],
        ['about_image', 'about.jpg', 'About Us Image', 'Image for the About Us page'],
        ['contact_image', 'contact.jpg', 'Contact Us Image', 'Image for the Contact Us page'],
        ['products_banner', 'products-banner.jpg', 'Products Banner', 'Banner image for the products page'],
        ['order_banner', 'order-banner.jpg', 'Order Banner', 'Banner image for the order page'],
        ['track_order_banner', 'track-order-banner.jpg', 'Track Order Banner', 'Banner image for the track order page'],
        ['terms_banner', 'terms-banner.jpg', 'Terms Banner', 'Banner image for the terms and conditions page'],
        ['privacy_banner', 'privacy-banner.jpg', 'Privacy Banner', 'Banner image for the privacy policy page'],
        ['checkout_header', 'checkout-header.jpg', 'Checkout Header', 'Header image for the checkout page']
    ];

    $insert_query = "INSERT INTO site_images (image_key, image_path, title, description) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $insert_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ssss', $image_key, $image_path, $title, $description);

        $success_count = 0;
        foreach ($default_images as $image) {
            $image_key = $image[0];
            $image_path = $image[1];
            $title = $image[2];
            $description = $image[3];

            if (mysqli_stmt_execute($stmt)) {
                $success_count++;
            }
        }

        mysqli_stmt_close($stmt);
        echo "<p>$success_count default images inserted successfully.</p>";
    } else {
        echo "<p>Error preparing statement: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>Table 'site_images' already has data.</p>";
}

// 5. Check if uploads directory exists
$uploads_dir = 'uploads/site';
if (!file_exists($uploads_dir)) {
    if (mkdir($uploads_dir, 0755, true)) {
        echo "<p>Created uploads directory: $uploads_dir</p>";
    } else {
        echo "<p>Failed to create uploads directory: $uploads_dir</p>";
    }
} else {
    echo "<p>Uploads directory already exists: $uploads_dir</p>";
}

// 6. Check PHP version
echo "<h2>System Information</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>MySQL Version: " . mysqli_get_server_info($conn) . "</p>";
echo "<p>Server: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";

// 7. Check if PASSWORD_ARGON2ID is available
if (defined('PASSWORD_ARGON2ID')) {
    echo "<p>PASSWORD_ARGON2ID is available.</p>";
} else {
    echo "<p>PASSWORD_ARGON2ID is not available. Using PASSWORD_DEFAULT instead.</p>";
}

echo "<h2>All fixes completed!</h2>";
echo "<p>You can now <a href='" . site_url('admin/index.php') . "'>login to the admin panel</a>.</p>";
