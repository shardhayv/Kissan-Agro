<?php

/**
 * Session Security Functions
 *
 * This file contains functions to enhance session security.
 */

/**
 * Initialize secure session
 *
 * @return void
 */
function init_secure_session()
{
    // Only configure session if it hasn't been started yet
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        $session_name = 'KISSANSESSID'; // Custom session name
        $secure = false; // Set to true if using HTTPS
        $httponly = true; // Prevent JavaScript access to session cookie

        // Force session to use cookies only
        if (ini_set('session.use_only_cookies', 1) === false) {
            error_log("Could not initiate a secure session (cookies)");
        }

        // Get current cookie parameters
        $cookieParams = session_get_cookie_params();

        // Set the cookie parameters
        session_set_cookie_params(
            $cookieParams["lifetime"],
            $cookieParams["path"],
            $cookieParams["domain"],
            $secure,
            $httponly
        );

        // Set the session name
        session_name($session_name);

        // Start the session
        session_start();
    }

    // Regenerate session ID periodically to prevent session fixation
    if (!isset($_SESSION['last_regeneration'])) {
        regenerate_session_id();
    } else {
        // Regenerate session ID every 30 minutes
        $interval = 30 * 60;
        if (time() - $_SESSION['last_regeneration'] > $interval) {
            regenerate_session_id();
        }
    }
}

/**
 * Regenerate session ID
 *
 * @return void
 */
function regenerate_session_id()
{
    // Save old session data
    $old_session_data = $_SESSION;

    // Regenerate session ID
    session_regenerate_id(true);

    // Restore session data
    $_SESSION = $old_session_data;

    // Update last regeneration time
    $_SESSION['last_regeneration'] = time();
}

/**
 * Validate user session
 *
 * @return bool True if session is valid, false otherwise
 */
function validate_session()
{
    // Check if user agent is consistent
    if (isset($_SESSION['user_agent'])) {
        if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            // User agent changed, possible session hijacking
            session_destroy();
            return false;
        }
    } else {
        // Set user agent
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    }

    // Check if IP address is consistent (optional, can cause issues with dynamic IPs)
    if (isset($_SESSION['ip_address'])) {
        if ($_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
            // IP address changed, possible session hijacking
            // Uncomment to enable this check (may cause issues with dynamic IPs)
            // session_destroy();
            // return false;
        }
    } else {
        // Set IP address
        $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
    }

    return true;
}

/**
 * Destroy session securely
 *
 * @return void
 */
function destroy_session()
{
    // Unset all session variables
    $_SESSION = array();

    // Get session parameters
    $params = session_get_cookie_params();

    // Delete the session cookie
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );

    // Destroy session
    session_destroy();
}

// Initialize secure session with error handling
try {
    init_secure_session();

    // Validate session
    validate_session();
} catch (Exception $e) {
    // Log error
    error_log("Session security error: " . $e->getMessage());

    // Start a basic session if secure session fails
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
