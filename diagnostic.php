<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Session and Environment Diagnostic</h1>";
echo "<p>This file helps diagnose issues with PHP sessions and environment settings.</p>";

// Check PHP version
echo "<h2>PHP Version</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

// Check session settings
echo "<h2>Session Settings</h2>";
echo "<pre>";
$sessionSettings = [
    'session.save_path' => ini_get('session.save_path'),
    'session.name' => ini_get('session.name'),
    'session.cookie_lifetime' => ini_get('session.cookie_lifetime'),
    'session.cookie_path' => ini_get('session.cookie_path'),
    'session.cookie_domain' => ini_get('session.cookie_domain'),
    'session.cookie_secure' => ini_get('session.cookie_secure'),
    'session.cookie_httponly' => ini_get('session.cookie_httponly'),
    'session.use_cookies' => ini_get('session.use_cookies'),
    'session.use_only_cookies' => ini_get('session.use_only_cookies'),
    'session.gc_maxlifetime' => ini_get('session.gc_maxlifetime'),
    'session.gc_probability' => ini_get('session.gc_probability'),
    'session.gc_divisor' => ini_get('session.gc_divisor'),
];

foreach ($sessionSettings as $key => $value) {
    echo "$key: $value\n";
}

echo "</pre>";

// Check session directory
echo "<h2>Session Directory</h2>";
$sessionPath = ini_get('session.save_path');
if (empty($sessionPath)) {
    echo "<p>No session.save_path set</p>";
} else {
    echo "<p>session.save_path: $sessionPath</p>";

    if (is_dir($sessionPath)) {
        echo "<p>Directory exists</p>";

        // Check if directory is writable
        if (is_writable($sessionPath)) {
            echo "<p>Directory is writable</p>";
        } else {
            echo "<p style='color: red;'>Directory is NOT writable</p>";
        }

        // List some session files
        $files = glob($sessionPath . '/sess_*');
        if (count($files) > 0) {
            echo "<p>Session files exist (" . count($files) . " files)</p>";
        } else {
            echo "<p>No session files found</p>";
        }
    } else {
        echo "<p style='color: red;'>Directory does NOT exist</p>";
    }
}

// Test session
echo "<h2>Session Test</h2>";
session_start();

// Check if session is working
if (isset($_SESSION['test_time'])) {
    echo "<p>Previous session test time: " . $_SESSION['test_time'] . "</p>";
} else {
    echo "<p>No previous session test found</p>";
}

// Set a new session value
$_SESSION['test_time'] = date('Y-m-d H:i:s');
echo "<p>New session test time set: " . $_SESSION['test_time'] . "</p>";

// Check session ID
echo "<p>Session ID: " . session_id() . "</p>";

// Test crypto functions
echo "<h2>Cryptographic Functions</h2>";
echo "<p>Testing secure random generation methods...</p>";
echo "<ul>";

// Test random_bytes
if (function_exists('random_bytes')) {
    try {
        $randomBytes = bin2hex(random_bytes(16));
        echo "<li>random_bytes(): Available and working - Result: $randomBytes</li>";
    } catch (Exception $e) {
        echo "<li style='color: red;'>random_bytes(): Available but ERROR - " . $e->getMessage() . "</li>";
    }
} else {
    echo "<li style='color: red;'>random_bytes(): Not available</li>";
}

// Test openssl_random_pseudo_bytes
if (function_exists('openssl_random_pseudo_bytes')) {
    try {
        $opensslRandom = bin2hex(openssl_random_pseudo_bytes(16));
        echo "<li>openssl_random_pseudo_bytes(): Available and working - Result: $opensslRandom</li>";
    } catch (Exception $e) {
        echo "<li style='color: red;'>openssl_random_pseudo_bytes(): Available but ERROR - " . $e->getMessage() . "</li>";
    }
} else {
    echo "<li style='color: red;'>openssl_random_pseudo_bytes(): Not available</li>";
}

// Test fallback methods
$fallbackRandom = md5(uniqid(mt_rand(), true));
echo "<li>Fallback method (md5 + uniqid + mt_rand): Result: $fallbackRandom</li>";

echo "</ul>";

// Test file paths and includes
echo "<h2>File System Tests</h2>";

$filesToCheck = [
    'includes/functions.php',
    'includes/form_security.php',
    'includes/prevent_resubmission.php',
    'includes/header.php',
    'includes/footer.php',
    'config/database.php',
    'config/environment.php'
];

echo "<ul>";
foreach ($filesToCheck as $file) {
    $fullPath = dirname(__FILE__) . '/' . $file;
    if (file_exists($fullPath)) {
        echo "<li>$file: Exists</li>";
    } else {
        echo "<li style='color: red;'>$file: Does NOT exist (checked path: $fullPath)</li>";
    }
}
echo "</ul>";

// Check uploads directory
echo "<h2>Uploads Directory</h2>";
$uploadsDir = dirname(__FILE__) . '/uploads/site';
if (is_dir($uploadsDir)) {
    echo "<p>Uploads directory exists</p>";
    if (is_writable($uploadsDir)) {
        echo "<p>Uploads directory is writable</p>";
    } else {
        echo "<p style='color: red;'>Uploads directory is NOT writable</p>";
    }
} else {
    echo "<p style='color: red;'>Uploads directory does NOT exist</p>";
}

// Check for site_images table in database
echo "<h2>Database Connection</h2>";
try {
    // Include database connection
    require_once dirname(__FILE__) . '/config/database.php';

    if (isset($conn) && $conn) {
        echo "<p>Database connection successful</p>";

        // Check site_images table
        $result = mysqli_query($conn, "SHOW TABLES LIKE 'site_images'");
        if (mysqli_num_rows($result) > 0) {
            echo "<p>site_images table exists</p>";

            // Check for required image keys
            $imageKeys = ['contact_header', 'checkout_header'];
            foreach ($imageKeys as $key) {
                $result = mysqli_query($conn, "SELECT * FROM site_images WHERE image_key = '$key'");
                if (mysqli_num_rows($result) > 0) {
                    echo "<p>Image key '$key' exists in database</p>";
                } else {
                    echo "<p style='color: red;'>Image key '$key' does NOT exist in database</p>";
                }
            }
        } else {
            echo "<p style='color: red;'>site_images table does NOT exist</p>";
        }
    } else {
        echo "<p style='color: red;'>Database connection failed</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database error: " . $e->getMessage() . "</p>";
}

echo "<p>Diagnostic completed at " . date('Y-m-d H:i:s') . "</p>";
