<?php

/**
 * Environment Configuration
 *
 * This file detects whether the application is running in development or production
 * and sets appropriate configuration values.
 */

// Define environment constants
define('ENV_DEVELOPMENT', 'development');
define('ENV_PRODUCTION', 'production');

// Detect environment based on server hostname
function detect_environment()
{
    $hostname = $_SERVER['HTTP_HOST'] ?? '';

    // Production environment
    if (strpos($hostname, 'shardhayvatshyayan.com') !== false) {
        return ENV_PRODUCTION;
    }

    // Default to development
    return ENV_DEVELOPMENT;
}

// Get current environment
$current_environment = detect_environment();

// Set base URL and path prefix based on environment
if ($current_environment === ENV_PRODUCTION) {
    // Production settings
    define('BASE_URL', 'https://shardhayvatshyayan.com');
    define('PATH_PREFIX', ''); // No prefix in production

    // Database settings for production
    define('DB_HOST', 'localhost');
    define('DB_USER', 'shardhay');
    define('DB_PASS', 'Mahakali@5254');
    define('DB_NAME', 'shardhay_kissan_agro_foods');
} else {
    // Development settings
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
    define('BASE_URL', 'http://' . $host);
    define('PATH_PREFIX', '/mill'); // Use /mill prefix in development

    // Database settings for development
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'kissan_agro_foods');
}

/**
 * Generate a URL that works in both development and production environments
 *
 * @param string $path The path relative to the site root
 * @return string The full URL
 */
function site_url($path = '')
{
    // Remove leading slash if present
    $path = ltrim($path, '/');

    // Add path prefix if not empty
    $prefix = PATH_PREFIX ? PATH_PREFIX . '/' : '';

    return BASE_URL . '/' . $prefix . $path;
}

/**
 * Generate an asset URL that works in both development and production environments
 * Includes cache busting parameter for better cache control
 *
 * @param string $path The path relative to the assets directory
 * @return string The full asset URL with cache busting
 */
function asset_url($path = '')
{
    // Remove leading slash if present
    $path = ltrim($path, '/');

    // Add cache busting parameter based on file modification time
    $file_path = __DIR__ . '/../assets/' . $path;
    $version = file_exists($file_path) ? filemtime($file_path) : time();

    return site_url('assets/' . $path) . '?v=' . $version;
}

/**
 * Generate an upload URL that works in both development and production environments
 *
 * @param string $path The path relative to the uploads directory
 * @return string The full upload URL
 */
function upload_url($path = '')
{
    // Remove leading slash if present
    $path = ltrim($path, '/');

    return site_url('uploads/' . $path);
}

/**
 * Check if the application is running in production
 *
 * @return bool True if in production, false otherwise
 */
function is_production()
{
    global $current_environment;
    return $current_environment === ENV_PRODUCTION;
}

/**
 * Check if the application is running in development
 *
 * @return bool True if in development, false otherwise
 */
function is_development()
{
    global $current_environment;
    return $current_environment === ENV_DEVELOPMENT;
}
