<?php

/**
 * Common Functions
 *
 * This file contains common functions used throughout the website.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include visitor tracker and database functions
require_once __DIR__ . '/visitor_tracker.php';
require_once __DIR__ . '/db_functions.php';

/**
 * Sanitize user input for display (XSS prevention)
 *
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize($data)
{
    if (is_array($data)) {
        // Recursively sanitize arrays
        foreach ($data as $key => $value) {
            $data[$key] = sanitize($value);
        }
        return $data;
    }

    // For strings
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize data for database (SQL Injection prevention)
 * Note: This is a fallback. Prefer using prepared statements with db_query()
 *
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize_sql($data)
{
    global $conn;

    if (is_array($data)) {
        // Recursively sanitize arrays
        foreach ($data as $key => $value) {
            $data[$key] = sanitize_sql($value);
        }
        return $data;
    }

    // For strings
    $data = trim($data);
    if ($conn) {
        $data = mysqli_real_escape_string($conn, $data);
    }
    return $data;
}

/**
 * Redirect to a specific page
 *
 * @param string $location The URL to redirect to
 */
function redirect($location)
{
    // Check if headers have already been sent
    if (headers_sent()) {
        // Use JavaScript for redirection
        echo '<script>window.location.href="' . $location . '";</script>';
        // Fallback for browsers with JavaScript disabled
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $location . '"></noscript>';
        exit;
    } else {
        // Use standard header redirect if headers haven't been sent
        header("Location: $location");
        exit;
    }
}

/**
 * Display success message
 *
 * @param string $message The message to display
 */
function set_success_message($message)
{
    $_SESSION['success_message'] = $message;
}

/**
 * Display error message
 *
 * @param string $message The message to display
 */
function set_error_message($message)
{
    $_SESSION['error_message'] = $message;
}

/**
 * Display messages and clear them from session
 */
function display_messages()
{
    if (isset($_SESSION['success_message'])) {
        echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
}

/**
 * Get setting value from database
 *
 * @param string $key The setting key
 * @param string $default Default value if setting not found
 * @return string The setting value
 */
function get_setting($key, $default = '')
{
    $key = sanitize_sql($key);

    // Use prepared statement to prevent SQL injection
    $query = "SELECT setting_value FROM settings WHERE setting_key = ? LIMIT 1";
    $row = db_select_one($query, 's', [$key]);

    if ($row) {
        return $row['setting_value'];
    }

    return $default;
}

/**
 * Get all products or products by category
 *
 * @param int $category_id Optional category ID to filter by
 * @param int $limit Optional limit of products to return
 * @param bool $featured Optional flag to get only featured products
 * @return array Array of products
 */
function get_products($category_id = null, $limit = null, $featured = false)
{
    global $conn;

    $query = "SELECT p.*, c.name as category_name FROM products p
              JOIN categories c ON p.category_id = c.id";

    if ($category_id) {
        $query .= " WHERE p.category_id = " . (int)$category_id;
    }

    if ($featured) {
        $query .= $category_id ? " AND p.is_featured = 1" : " WHERE p.is_featured = 1";
    }

    $query .= " ORDER BY p.created_at DESC";

    if ($limit) {
        $query .= " LIMIT " . (int)$limit;
    }

    $result = mysqli_query($conn, $query);
    $products = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }

    return $products;
}

/**
 * Get all categories
 *
 * @return array Array of categories
 */
function get_categories()
{
    global $conn;

    $query = "SELECT * FROM categories ORDER BY name";
    $result = mysqli_query($conn, $query);
    $categories = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }

    return $categories;
}

/**
 * Get product by ID
 *
 * @param int $id Product ID
 * @return array|null Product data or null if not found
 */
function get_product($id)
{
    global $conn;
    $id = (int)$id;

    $query = "SELECT p.*, c.name as category_name FROM products p
              JOIN categories c ON p.category_id = c.id
              WHERE p.id = $id";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }

    return null;
}

/**
 * Format price with currency symbol
 *
 * @param float $price The price to format
 * @return string Formatted price
 */
function format_price($price)
{
    return '₹' . number_format($price, 2);
}

/**
 * Check if user is logged in
 *
 * @return bool True if user is logged in, false otherwise
 */
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Check if current user is admin
 *
 * @return bool True if user is admin, false otherwise
 */
function is_admin()
{
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current page URL
 *
 * @return string Current page URL
 */
function current_url()
{
    $request_uri = $_SERVER['REQUEST_URI'];

    // Remove /mill prefix if in development
    if (is_development() && strpos($request_uri, PATH_PREFIX) === 0) {
        $request_uri = substr($request_uri, strlen(PATH_PREFIX));
    }

    return BASE_URL . $request_uri;
}

/**
 * Generate a random string
 *
 * @param int $length Length of the string
 * @return string Random string
 */
function generate_random_string($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $string;
}

/**
 * Get site image by key
 *
 * @param string $key The image key
 * @param string $default Default image path if not found
 * @return string The image path
 */
function get_site_image($key, $default = '')
{
    $key = sanitize_sql($key);

    // Use prepared statement to prevent SQL injection
    $query = "SELECT image_path FROM site_images WHERE image_key = ? LIMIT 1";
    $row = db_select_one($query, 's', [$key]);

    if ($row) {
        return site_url("uploads/site/{$row['image_path']}");
    }

    return $default ? asset_url("images/{$default}") : '';
}

/**
 * Get all site images
 *
 * @return array Array of site images
 */
function get_all_site_images()
{
    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM site_images ORDER BY image_key";
    $images = db_select($query);

    return $images ?: [];
}

/**
 * Generate SEO-friendly URL slug
 *
 * @param string $string The string to convert to a slug
 * @return string The SEO-friendly slug
 */
function generate_slug($string)
{
    // Replace non-alphanumeric characters with hyphens
    $string = preg_replace('/[^a-zA-Z0-9]/', '-', $string);
    // Replace multiple hyphens with a single hyphen
    $string = preg_replace('/-+/', '-', $string);
    // Remove hyphens from the beginning and end
    $string = trim($string, '-');
    // Convert to lowercase
    $string = strtolower($string);

    return $string;
}

/**
 * Get meta description for a product
 *
 * @param array $product The product data
 * @return string The meta description
 */
function get_product_meta_description($product)
{
    if (!$product) {
        return '';
    }

    // Create a meta description from the product description
    $description = strip_tags($product['description']);
    $description = substr($description, 0, 160); // Limit to 160 characters

    // Add ellipsis if the description was truncated
    if (strlen(strip_tags($product['description'])) > 160) {
        $description .= '...';
    }

    return $description;
}

/**
 * Add structured data for a product
 *
 * @param array $product The product data
 * @return array The structured data
 */
function get_product_structured_data($product)
{
    if (!$product) {
        return [];
    }

    $image_url = !empty($product['image'])
        ? upload_url($product['image'])
        : asset_url('images/product-placeholder.jpg');

    return [
        "@context" => "https://schema.org",
        "@type" => "Product",
        "name" => $product['name'],
        "image" => $image_url,
        "description" => strip_tags($product['description']),
        "sku" => "KAF-" . $product['id'],
        "brand" => [
            "@type" => "Brand",
            "name" => "Kissan Agro Foods"
        ],
        "offers" => [
            "@type" => "Offer",
            "url" => site_url("products.php?id=" . $product['id']),
            "priceCurrency" => "INR",
            "price" => $product['price'],
            "availability" => $product['stock'] > 0 ? "https://schema.org/InStock" : "https://schema.org/OutOfStock",
            "seller" => [
                "@type" => "Organization",
                "name" => "Kissan Agro Foods"
            ]
        ]
    ];
}
