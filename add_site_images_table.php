<?php
/**
 * Add Site Images Table
 * 
 * This script adds the site_images table to the database.
 */

// Include database connection
require_once 'config/database.php';

// Read the SQL file
$sql = file_get_contents('site_images.sql');

// Execute the SQL
if (mysqli_multi_query($conn, $sql)) {
    echo "Site images table created successfully.<br>";
    
    // Create uploads/site directory if it doesn't exist
    $upload_dir = 'uploads/site/';
    if (!file_exists($upload_dir)) {
        if (mkdir($upload_dir, 0777, true)) {
            echo "Created directory: $upload_dir<br>";
        } else {
            echo "Failed to create directory: $upload_dir<br>";
        }
    } else {
        echo "Directory already exists: $upload_dir<br>";
    }
    
    // Copy default images from assets/images to uploads/site
    $default_images = [
        'logo.png',
        'hero-bg.jpg',
        'about.jpg',
        'wheat-mill.jpg',
        'rice-mill.jpg',
        'team1.jpg',
        'team2.jpg',
        'team3.jpg'
    ];
    
    foreach ($default_images as $image) {
        $source = 'assets/images/' . $image;
        $destination = $upload_dir . $image;
        
        if (file_exists($source)) {
            if (copy($source, $destination)) {
                echo "Copied $source to $destination<br>";
            } else {
                echo "Failed to copy $source to $destination<br>";
            }
        } else {
            echo "Source file does not exist: $source<br>";
        }
    }
    
    echo "<p>Setup complete. <a href='/mill/admin/images.php'>Go to Image Management</a></p>";
} else {
    echo "Error creating site images table: " . mysqli_error($conn);
}
?>
