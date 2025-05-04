<?php
/**
 * Add Mill Images
 * 
 * This script adds the wheat_mill_image and rice_mill_image keys to the site_images table.
 */

// Include database connection
require_once 'config/database.php';
require_once 'includes/functions.php';

// Check if the wheat_mill_image already exists
$query = "SELECT id FROM site_images WHERE image_key = 'wheat_mill_image'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "The wheat_mill_image already exists in the database.<br>";
} else {
    // Get the wheat_mill image path
    $query = "SELECT image_path FROM site_images WHERE image_key = 'wheat_mill'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $image_path = $row['image_path'];
        
        // Insert into database with the same image path
        $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                  VALUES ('wheat_mill_image', '$image_path', 'Wheat Mill Image', 'Image of our wheat flour mill for homepage')";
        
        if (mysqli_query($conn, $query)) {
            echo "Added wheat_mill_image to the database using the same image as wheat_mill.<br>";
        } else {
            echo "Error adding wheat_mill_image to the database: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "The wheat_mill image does not exist in the database. Creating a new entry.<br>";
        
        // Insert into database with a placeholder
        $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                  VALUES ('wheat_mill_image', 'wheat-mill.jpg', 'Wheat Mill Image', 'Image of our wheat flour mill for homepage')";
        
        if (mysqli_query($conn, $query)) {
            echo "Added wheat_mill_image to the database.<br>";
            echo "Note: You will need to upload the actual image through the admin panel.<br>";
        } else {
            echo "Error adding wheat_mill_image to the database: " . mysqli_error($conn) . "<br>";
        }
    }
}

// Check if the rice_mill_image already exists
$query = "SELECT id FROM site_images WHERE image_key = 'rice_mill_image'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    echo "The rice_mill_image already exists in the database.<br>";
} else {
    // Get the rice_mill image path
    $query = "SELECT image_path FROM site_images WHERE image_key = 'rice_mill'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $image_path = $row['image_path'];
        
        // Insert into database with the same image path
        $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                  VALUES ('rice_mill_image', '$image_path', 'Rice Mill Image', 'Image of our puffed rice mill for homepage')";
        
        if (mysqli_query($conn, $query)) {
            echo "Added rice_mill_image to the database using the same image as rice_mill.<br>";
        } else {
            echo "Error adding rice_mill_image to the database: " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "The rice_mill image does not exist in the database. Creating a new entry.<br>";
        
        // Insert into database with a placeholder
        $query = "INSERT INTO site_images (image_key, image_path, title, description) 
                  VALUES ('rice_mill_image', 'rice-mill.jpg', 'Rice Mill Image', 'Image of our puffed rice mill for homepage')";
        
        if (mysqli_query($conn, $query)) {
            echo "Added rice_mill_image to the database.<br>";
            echo "Note: You will need to upload the actual image through the admin panel.<br>";
        } else {
            echo "Error adding rice_mill_image to the database: " . mysqli_error($conn) . "<br>";
        }
    }
}

echo "<p>Setup complete. <a href='/mill/admin/images.php'>Go to Image Management</a></p>";
?>
