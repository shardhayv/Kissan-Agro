<?php
// Error reporting for debugging (comment out in production)
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Include authentication functions
try {
    // Ensure proper session handling by including necessary files
    require_once '../includes/functions.php';
    require_once '../includes/auth.php';

    // Check if user is logged in before logging out
    if (isset($_SESSION['user_id'])) {
        // Store user info for logging
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? 'unknown';

        // Log out user but don't redirect within the function
        logout(false);
    }

    // Always redirect to login page after attempting logout
    // This ensures redirection happens even if session was already destroyed
    header('Location: ' . site_url('admin/index.php'));
    exit;
} catch (Exception $e) {
    // Log error
    error_log("Logout error: " . $e->getMessage());

    // Destroy session manually in case of error
    session_start();
    session_unset();
    session_destroy();

    // Redirect to login page
    header('Location: ' . site_url('admin/index.php'));
    exit;
}
