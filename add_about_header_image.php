<?php
/**
 * Add About Header Image
 * 
 * This script adds the about_header image to the site_images table.
 */

// Include database connection
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if the image already exists
$query = "SELECT id FROM site_images WHERE image_key = 'about_header'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "The about_header image already exists in the database.<br>";
} else {
    // Copy the default image from assets/images to uploads/site
    $source = 'assets/images/about-header.jpg';
    $destination = 'uploads/site/about-header.jpg';
    
    // Check if source file exists
    if (file_exists($source)) {
        // Create uploads/site directory if it doesn't exist
        $upload_dir = 'uploads/site/';
        if (!file_exists($upload_dir)) {
            if (mkdir($upload_dir, 0777, true)) {
                echo "Created directory: $upload_dir<br>";
            } else {
                echo "Failed to create directory: $upload_dir<br>";
            }
        }
        
        // Copy the file
        if (copy($source, $destination)) {
            echo "Copied $source to $destination<br>";
            
            // Insert into database
            $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                      VALUES ('about_header', 'about-header.jpg', 'About Page Header', 'Background image for the about page header')";
            
            if (mysqli_query($conn, $query)) {
                echo "Added about_header image to the database.<br>";
            } else {
                echo "Error adding about_header image to the database: " . mysqli_error($conn) . "<br>";
            }
        } else {
            echo "Failed to copy $source to $destination<br>";
        }
    } else {
        echo "Source file does not exist: $source<br>";
        echo "Creating a placeholder image record in the database...<br>";
        
        // Insert into database with a placeholder
        $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                  VALUES ('about_header', 'about-header.jpg', 'About Page Header', 'Background image for the about page header')";
        
        if (mysqli_query($conn, $query)) {
            echo "Added about_header image to the database.<br>";
            echo "Note: You will need to upload the actual image through the admin panel.<br>";
        } else {
            echo "Error adding about_header image to the database: " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<p>Setup complete. <a href='/mill/admin/images.php'>Go to Image Management</a></p>";
?>
