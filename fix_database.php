<?php
// Include database connection
require_once 'config/database.php';

// Create site_images table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS site_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_key VARCHAR(50) NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(100),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (image_key)
)";

if (mysqli_query($conn, $query)) {
    echo "Table 'site_images' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Create security_logs table if it doesn't exist
$query = "CREATE TABLE IF NOT EXISTS security_logs (
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

if (mysqli_query($conn, $query)) {
    echo "Table 'security_logs' created successfully or already exists.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Insert default images if the site_images table is empty
$check_query = "SELECT COUNT(*) as count FROM site_images";
$result = mysqli_query($conn, $check_query);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
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
        
        foreach ($default_images as $image) {
            $image_key = $image[0];
            $image_path = $image[1];
            $title = $image[2];
            $description = $image[3];
            
            mysqli_stmt_execute($stmt);
        }
        
        mysqli_stmt_close($stmt);
        echo "Default images inserted successfully.<br>";
    } else {
        echo "Error preparing statement: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Table 'site_images' already has data.<br>";
}

echo "Database fixes completed!";
?>
