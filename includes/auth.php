<?php

/**
 * Authentication Functions
 *
 * This file contains functions related to user authentication.
 */

// Include database connection, common functions, form security, and security logger
require_once 'functions.php';
require_once __DIR__ . '/../config/database.php';
require_once 'form_security.php';
require_once 'security_logger.php';
require_once 'session_security.php'; // Add this line to include session_security.php

/**
 * Authenticate user with enhanced security
 *
 * @param string $username Username
 * @param string $password Password
 * @return bool True if authentication successful, false otherwise
 */
function login($username, $password)
{
    // Sanitize input
    $username = sanitize_sql($username);

    // Use prepared statement to prevent SQL injection
    $query = "SELECT * FROM users WHERE username = ? LIMIT 1";
    $user = db_select_one($query, 's', [$username]);

    if ($user) {
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];

            // Set login time for session timeout
            $_SESSION['login_time'] = time();

            // Regenerate session ID to prevent session fixation
            regenerate_session_id();

            // Log successful login
            log_successful_login($user['id'], $user['username']);

            // Check if password needs rehashing (if using an older algorithm)
            // Use PASSWORD_ARGON2ID if available (PHP 7.3+), otherwise use PASSWORD_DEFAULT
            $hash_algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

            if (password_needs_rehash($user['password'], $hash_algo)) {
                // Update password hash to newer algorithm
                $new_hash = password_hash($password, $hash_algo);
                $query = "UPDATE users SET password = ? WHERE id = ?";
                db_execute($query, 'si', [$new_hash, $user['id']]);
            }

            return true;
        } else {
            // Log failed login
            log_failed_login($username, 'Invalid password');
        }
    } else {
        // Log failed login
        log_failed_login($username, 'User not found');
    }

    return false;
}

/**
 * Log out user securely
 * 
 * @param bool $redirect Whether to redirect after logout (default: true)
 * @return void
 */
function logout($redirect = true)
{
    // Log logout event if user is logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        log_logout($_SESSION['user_id'], $_SESSION['username']);
    }

    // Use secure session destruction
    destroy_session();

    // Only redirect if the parameter is set to true
    if ($redirect) {
        // Redirect to login page
        redirect(site_url('admin/index.php'));
    }
}

/**
 * Register new user with enhanced security
 *
 * @param array $user_data User data
 * @return bool True if registration successful, false otherwise
 */
function register_user($user_data)
{
    // Validate password strength
    if (!validate_password_strength($user_data['password'])) {
        set_error_message('Password must be at least 8 characters long and include uppercase, lowercase, and numbers');
        return false;
    }

    // Validate email format
    if (!validate_email($user_data['email'])) {
        set_error_message('Please enter a valid email address');
        return false;
    }

    // Sanitize input
    $username = sanitize_sql($user_data['username']);
    $email = sanitize_sql($user_data['email']);
    $full_name = sanitize_sql($user_data['full_name']);
    $role = sanitize_sql($user_data['role']);

    // Use PASSWORD_ARGON2ID if available (PHP 7.3+), otherwise use PASSWORD_DEFAULT
    $hash_algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

    // Hash password with strong algorithm and options
    $password_hash = password_hash($user_data['password'], $hash_algo);

    // Check if username already exists using prepared statement
    $query = "SELECT id FROM users WHERE username = ? LIMIT 1";
    $user = db_select_one($query, 's', [$username]);

    if ($user) {
        set_error_message('Username already exists');
        return false;
    }

    // Check if email already exists using prepared statement
    $query = "SELECT id FROM users WHERE email = ? LIMIT 1";
    $user = db_select_one($query, 's', [$email]);

    if ($user) {
        set_error_message('Email already exists');
        return false;
    }

    // Insert new user using prepared statement
    $query = "INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)";
    $result = db_execute($query, 'sssss', [$username, $password_hash, $email, $full_name, $role]);

    if ($result) {
        set_success_message('User registered successfully');
        return true;
    } else {
        set_error_message('Error registering user. Please try again.');
        return false;
    }
}

/**
 * Update user profile with enhanced security
 *
 * @param array $user_data User data
 * @return bool True if update successful, false otherwise
 */
function update_user($user_data)
{
    // Validate input
    $id = (int)$user_data['id'];

    // Validate email format
    if (!validate_email($user_data['email'])) {
        set_error_message('Please enter a valid email address');
        return false;
    }

    // Sanitize input
    $email = sanitize_sql($user_data['email']);
    $full_name = sanitize_sql($user_data['full_name']);

    // Start with base parameters
    $params = [$email, $full_name];
    $types = 'ss';
    $set_clause = "email = ?, full_name = ?";

    // Update password if provided
    if (!empty($user_data['password'])) {
        // Validate password strength
        if (!validate_password_strength($user_data['password'])) {
            set_error_message('Password must be at least 8 characters long and include uppercase, lowercase, and numbers');
            return false;
        }

        // Use PASSWORD_ARGON2ID if available (PHP 7.3+), otherwise use PASSWORD_DEFAULT
        $hash_algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;

        // Hash password with strong algorithm
        $password_hash = password_hash($user_data['password'], $hash_algo);
        $set_clause .= ", password = ?";
        $types .= 's';
        $params[] = $password_hash;
    }

    // Update role if admin
    if (is_admin() && isset($user_data['role'])) {
        $role = sanitize_sql($user_data['role']);
        $set_clause .= ", role = ?";
        $types .= 's';
        $params[] = $role;
    }

    // Add ID to parameters
    $params[] = $id;
    $types .= 'i';

    // Update user using prepared statement
    $query = "UPDATE users SET $set_clause WHERE id = ?";
    $result = db_execute($query, $types, $params);

    if ($result) {
        set_success_message('User updated successfully');
        return true;
    } else {
        set_error_message('Error updating user. Please try again.');
        return false;
    }
}

/**
 * Delete user securely
 *
 * @param int $id User ID
 * @return bool True if deletion successful, false otherwise
 */
function delete_user($id)
{
    $id = (int)$id;

    // Prevent deleting own account
    if ($id === $_SESSION['user_id']) {
        set_error_message('You cannot delete your own account');
        return false;
    }

    // Delete user using prepared statement
    $query = "DELETE FROM users WHERE id = ?";
    $result = db_execute($query, 'i', [$id]);

    if ($result) {
        set_success_message('User deleted successfully');
        return true;
    } else {
        set_error_message('Error deleting user. Please try again.');
        return false;
    }
}

/**
 * Get user by ID securely
 *
 * @param int $id User ID
 * @return array|null User data or null if not found
 */
function get_user($id)
{
    $id = (int)$id;

    // Get user using prepared statement
    $query = "SELECT id, username, email, full_name, role, created_at FROM users WHERE id = ?";
    return db_select_one($query, 'i', [$id]);
}

/**
 * Get all users securely
 *
 * @return array Array of users
 */
function get_users()
{
    // Get all users using prepared statement
    $query = "SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id";
    $users = db_select($query);

    return $users ?: [];
}

/**
 * Check if user has access to a specific page with enhanced security
 *
 * @param array $allowed_roles Array of allowed roles
 * @param string $resource_name Name of the resource being accessed (for logging)
 * @return bool True if user has access, false otherwise
 */
function check_access($allowed_roles = ['admin'], $resource_name = 'admin page')
{
    if (!is_logged_in()) {
        // Log access violation
        log_access_violation($resource_name, implode(', ', $allowed_roles));

        set_error_message('You must be logged in to access this page');
        redirect(site_url('admin/index.php'));
        return false;
    }

    if (!in_array($_SESSION['user_role'], $allowed_roles)) {
        // Log access violation
        log_access_violation($resource_name, implode(', ', $allowed_roles));

        set_error_message('You do not have permission to access this page');
        redirect(site_url('admin/dashboard.php'));
        return false;
    }

    return true;
}

/**
 * Generate CSRF token (for backward compatibility)
 * Uses the more secure form token system
 *
 * @return string CSRF token
 */
function generate_csrf_token()
{
    // Use the form token system for CSRF protection
    return generate_form_token('csrf_global');
}

/**
 * Verify CSRF token (for backward compatibility)
 *
 * @param string $token Token to verify
 * @return bool True if token is valid, false otherwise
 */
function verify_csrf_token($token)
{
    // Use the form token validation system
    return validate_form_token('csrf_global', $token, 7200); // 2 hour expiry
}

/**
 * Output CSRF token field (for backward compatibility)
 *
 * @return void
 */
function csrf_token_field()
{
    // Use the form token field system
    form_token_field('csrf_global');
}

/**
 * Check CSRF token in POST request (for backward compatibility)
 *
 * @return bool True if token is valid, false otherwise
 */
function check_csrf_token()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // For backward compatibility, check both systems
        if (isset($_POST['form_token']) && isset($_POST['form_name'])) {
            // New system
            if ($_POST['form_name'] === 'csrf_global' && validate_form_token('csrf_global', $_POST['form_token'], 7200)) {
                return true;
            }
        }

        // Old system
        if (isset($_POST['csrf_token'])) {
            if (verify_csrf_token($_POST['csrf_token'])) {
                return true;
            }
        }

        // Log CSRF failure
        $form_name = $_POST['form_name'] ?? 'unknown';
        log_csrf_failure($form_name);

        set_error_message('Invalid security token. Please try again.');
        return false;
    }
    return true;
}
