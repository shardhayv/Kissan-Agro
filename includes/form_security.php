<?php

/**
 * Form Security Functions
 *
 * This file contains functions to enhance form security.
 */

/**
 * Generate a unique form token
 * 
 * @param string $form_name Name of the form
 * @return string Form token
 */
function generate_form_token($form_name)
{
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    try {
        // Try to use random_bytes (PHP 7+)
        if (function_exists('random_bytes')) {
            $token = bin2hex(random_bytes(32));
        }
        // Fallback to openssl_random_pseudo_bytes
        elseif (function_exists('openssl_random_pseudo_bytes')) {
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }
        // Last resort fallback
        else {
            $token = md5(uniqid(mt_rand(), true) . microtime(true));
        }
    } catch (Exception $e) {
        // If any exceptions occur, use a fallback method
        error_log("Error generating secure token: " . $e->getMessage());
        $token = md5(uniqid(mt_rand(), true) . microtime(true));
    }

    // Store token in session
    if (!isset($_SESSION['form_tokens'])) {
        $_SESSION['form_tokens'] = array();
    }

    $_SESSION['form_tokens'][$form_name] = [
        'token' => $token,
        'time' => time()
    ];

    return $token;
}

/**
 * Validate form token
 * 
 * @param string $form_name Name of the form
 * @param string $token Token to validate
 * @param int $expiry Token expiry time in seconds (default: 3600 = 1 hour)
 * @return bool True if token is valid, false otherwise
 */
function validate_form_token($form_name, $token, $expiry = 3600)
{
    // Ensure session is started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if form_tokens exists in session
    if (!isset($_SESSION['form_tokens'])) {
        error_log("Form tokens array not found in session");
        return false;
    }

    // Check if token exists
    if (!isset($_SESSION['form_tokens'][$form_name])) {
        error_log("Form token not found for form: $form_name");
        return false;
    }

    // Get stored token
    $stored = $_SESSION['form_tokens'][$form_name];

    // Check if token has expired
    if (time() - $stored['time'] > $expiry) {
        // Token expired, remove it
        unset($_SESSION['form_tokens'][$form_name]);
        error_log("Form token expired for form: $form_name");
        return false;
    }

    // Validate token
    if ($token === $stored['token']) {
        // Token used, remove it to prevent reuse (one-time token)
        unset($_SESSION['form_tokens'][$form_name]);
        return true;
    }

    error_log("Invalid form token provided for form: $form_name");
    return false;
}

/**
 * Output form token field
 * 
 * @param string $form_name Name of the form
 * @return void
 */
function form_token_field($form_name)
{
    $token = generate_form_token($form_name);
    echo '<input type="hidden" name="form_token" value="' . $token . '">';
    echo '<input type="hidden" name="form_name" value="' . htmlspecialchars($form_name) . '">';
}

/**
 * Check form token in POST request
 * 
 * @return bool True if token is valid, false otherwise
 */
function check_form_token()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['form_token']) || !isset($_POST['form_name'])) {
            set_error_message('Invalid form submission. Please try again.');
            return false;
        }

        $token = $_POST['form_token'];
        $form_name = $_POST['form_name'];

        if (!validate_form_token($form_name, $token)) {
            set_error_message('Form session expired or invalid. Please try again.');
            return false;
        }

        return true;
    }

    return false;
}

/**
 * Validate required form fields
 * 
 * @param array $required_fields Array of required field names
 * @param array $data Form data
 * @return array Array of error messages
 */
function validate_required_fields($required_fields, $data)
{
    $errors = [];

    foreach ($required_fields as $field) {
        if (!isset($data[$field]) || trim($data[$field]) === '') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }

    return $errors;
}

/**
 * Validate email field
 * 
 * @param string $email Email to validate
 * @return bool True if email is valid, false otherwise
 */
function validate_email($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number
 * 
 * @param string $phone Phone number to validate
 * @return bool True if phone number is valid, false otherwise
 */
function validate_phone($phone)
{
    // Basic phone validation (adjust as needed for your region)
    return preg_match('/^[0-9+\-\s()]{7,20}$/', $phone) === 1;
}

/**
 * Validate password strength
 * 
 * @param string $password Password to validate
 * @param int $min_length Minimum password length
 * @return bool True if password is strong enough, false otherwise
 */
function validate_password_strength($password, $min_length = 8)
{
    // Check length
    if (strlen($password) < $min_length) {
        return false;
    }

    // Check for at least one uppercase letter, one lowercase letter, and one number
    if (
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password)
    ) {
        return false;
    }

    return true;
}

/**
 * Clean and validate file upload
 * 
 * @param array $file File from $_FILES
 * @param array $allowed_types Allowed MIME types
 * @param int $max_size Maximum file size in bytes
 * @return array|bool Array with file info or false on error
 */
function validate_file_upload($file, $allowed_types, $max_size = 5242880)
{
    // Check if file was uploaded
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    // Check file size
    if ($file['size'] > $max_size) {
        return false;
    }

    // Check file type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $file_type = $finfo->file($file['tmp_name']);

    if (!in_array($file_type, $allowed_types)) {
        return false;
    }

    // Generate a safe filename
    $file_name = preg_replace('/[^a-zA-Z0-9_.-]/', '', $file['name']);
    $file_name = strtolower(basename($file_name));

    // Add a unique identifier to prevent overwriting
    $file_name = time() . '_' . $file_name;

    return [
        'name' => $file_name,
        'tmp_name' => $file['tmp_name'],
        'type' => $file_type,
        'size' => $file['size']
    ];
}
