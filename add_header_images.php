<?php

/**
 * Add Header Images
 *
 * This script adds all the necessary header image keys to the site_images table.
 */

// Include database connection
require_once 'config/database.php';
require_once 'includes/functions.php';

// Define all the header image keys we need
$header_images = [
    'about_header' => [
        'title' => 'About Page Header',
        'description' => 'Background image for the about page header'
    ],
    'products_header' => [
        'title' => 'Products Page Header',
        'description' => 'Background image for the products page header'
    ],
    'contact_header' => [
        'title' => 'Contact Page Header',
        'description' => 'Background image for the contact page header'
    ],
    'track_order_header' => [
        'title' => 'Track Order Page Header',
        'description' => 'Background image for the track order page header'
    ],
    'cart_header' => [
        'title' => 'Cart Page Header',
        'description' => 'Background image for the shopping cart page header'
    ],
    'terms_header' => [
        'title' => 'Terms Page Header',
        'description' => 'Background image for the terms and conditions page header'
    ],
    'privacy_header' => [
        'title' => 'Privacy Policy Page Header',
        'description' => 'Background image for the privacy policy page header'
    ],
    'checkout_header' => [
        'title' => 'Order/Checkout Page Header',
        'description' => 'Background image for the order/checkout page header'
    ]
];

// Check and add each header image
foreach ($header_images as $image_key => $image_info) {
    // Check if the image already exists
    $query = "SELECT id FROM site_images WHERE image_key = '$image_key'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        echo "The $image_key image already exists in the database.<br>";
    } else {
        // Create a placeholder filename based on the key
        $image_path = str_replace('_', '-', $image_key) . '.jpg';

        // Check if source file exists in assets/images
        $source = 'assets/images/' . $image_path;
        $destination = 'uploads/site/' . $image_path;

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
            } else {
                echo "Failed to copy $source to $destination<br>";
            }
        } else {
            echo "Source file does not exist: $source<br>";
        }

        // Insert into database
        $title = $image_info['title'];
        $description = $image_info['description'];
        $query = "INSERT INTO site_images (image_key, image_path, title, description)
                  VALUES ('$image_key', '$image_path', '$title', '$description')";

        if (mysqli_query($conn, $query)) {
            echo "Added $image_key to the database.<br>";
            echo "Note: You will need to upload the actual image through the admin panel.<br>";
        } else {
            echo "Error adding $image_key to the database: " . mysqli_error($conn) . "<br>";
        }

        echo "<hr>";
    }
}

echo "<p>Setup complete. <a href='/mill/admin/images.php'>Go to Image Management</a></p>";
