<?php

/**
 * Security Logger
 *
 * This file contains functions for logging security events.
 */

/**
 * Log a security event
 *
 * @param string $event_type Type of security event
 * @param string $description Description of the event
 * @param array $data Additional data about the event
 * @return bool True if log was successful, false otherwise
 */
function log_security_event($event_type, $description, $data = [])
{
    global $conn;

    // Check if the security_logs table exists
    $table_exists = false;
    $check_query = "SHOW TABLES LIKE 'security_logs'";
    $result = mysqli_query($conn, $check_query);
    if ($result && mysqli_num_rows($result) > 0) {
        $table_exists = true;
    }

    // If table doesn't exist, create it
    if (!$table_exists) {
        create_security_logs_table();
    }

    try {
        // Get user information
        $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
        $username = isset($_SESSION['username']) ? (function_exists('sanitize_sql') ? sanitize_sql($_SESSION['username']) : $_SESSION['username']) : 'guest';

        // Get IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];

        // Get user agent
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? (function_exists('sanitize_sql') ? sanitize_sql($_SERVER['HTTP_USER_AGENT']) : $_SERVER['HTTP_USER_AGENT']) : '';

        // Get request URI
        $request_uri = isset($_SERVER['REQUEST_URI']) ? (function_exists('sanitize_sql') ? sanitize_sql($_SERVER['REQUEST_URI']) : $_SERVER['REQUEST_URI']) : '';

        // Serialize additional data
        $data_json = !empty($data) ? json_encode($data) : null;

        // Check if db_execute function exists
        if (function_exists('db_execute')) {
            // Insert log entry using prepared statement
            $query = "INSERT INTO security_logs (event_type, description, user_id, username, ip_address, user_agent, request_uri, additional_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

            $result = db_execute($query, 'ssissss', [
                $event_type,
                $description,
                $user_id,
                $username,
                $ip_address,
                $user_agent,
                $request_uri,
                $data_json
            ]);

            return $result !== false;
        } else {
            // Fallback to direct mysqli query
            $stmt = mysqli_prepare($conn, "INSERT INTO security_logs (event_type, description, user_id, username, ip_address, user_agent, request_uri, additional_data)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            mysqli_stmt_bind_param(
                $stmt,
                'ssissss',
                $event_type,
                $description,
                $user_id,
                $username,
                $ip_address,
                $user_agent,
                $request_uri,
                $data_json
            );

            $result = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            return $result;
        }
    } catch (Exception $e) {
        // Log error to PHP error log
        error_log("Error logging security event: " . $e->getMessage());
        return false;
    }
}

/**
 * Log a failed login attempt
 *
 * @param string $username Username that was attempted
 * @param string $reason Reason for the failure
 * @return bool True if log was successful, false otherwise
 */
function log_failed_login($username, $reason = 'Invalid credentials')
{
    return log_security_event('failed_login', "Failed login attempt for user: {$username}", [
        'reason' => $reason,
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}

/**
 * Log a successful login
 *
 * @param int $user_id User ID
 * @param string $username Username
 * @return bool True if log was successful, false otherwise
 */
function log_successful_login($user_id, $username)
{
    return log_security_event('successful_login', "Successful login for user: {$username}", [
        'user_id' => $user_id,
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}

/**
 * Log a logout event
 *
 * @param int $user_id User ID
 * @param string $username Username
 * @return bool True if log was successful, false otherwise
 */
function log_logout($user_id, $username)
{
    return log_security_event('logout', "User logged out: {$username}", [
        'user_id' => $user_id
    ]);
}

/**
 * Log a CSRF token failure
 *
 * @param string $form_name Name of the form
 * @return bool True if log was successful, false otherwise
 */
function log_csrf_failure($form_name)
{
    return log_security_event('csrf_failure', "CSRF token validation failed for form: {$form_name}", [
        'form_name' => $form_name,
        'method' => $_SERVER['REQUEST_METHOD']
    ]);
}

/**
 * Log a suspicious request
 *
 * @param string $reason Reason the request is suspicious
 * @param array $request_data Request data
 * @return bool True if log was successful, false otherwise
 */
function log_suspicious_request($reason, $request_data = [])
{
    return log_security_event('suspicious_request', $reason, $request_data);
}

/**
 * Log an access violation
 *
 * @param string $resource Resource that was attempted to be accessed
 * @param string $required_role Role required to access the resource
 * @return bool True if log was successful, false otherwise
 */
function log_access_violation($resource, $required_role)
{
    $user_role = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'guest';

    return log_security_event('access_violation', "Unauthorized access attempt to {$resource}", [
        'required_role' => $required_role,
        'user_role' => $user_role
    ]);
}

/**
 * Create security_logs table if it doesn't exist
 */
function create_security_logs_table()
{
    global $conn;

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

    mysqli_query($conn, $query);
}

// Create security_logs table if it doesn't exist
create_security_logs_table();
