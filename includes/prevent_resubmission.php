<?php

/**
 * Prevent Form Resubmission
 * 
 * This file contains functions to prevent form resubmission on page reload.
 * Include this file at the top of any page that processes form submissions.
 */

// Include form security functions
require_once 'form_security.php';

/**
 * Check if a form has been submitted and process it only once
 * 
 * @param string $form_name Name of the form
 * @param callable $process_function Function to process the form
 * @param string $redirect_url URL to redirect to after processing
 * @param array $redirect_params Optional parameters to add to redirect URL
 * @return bool True if form was processed, false otherwise
 */
function process_form_once($form_name, $process_function, $redirect_url, $redirect_params = [])
{
    // Check if form was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Check if this is a form submission
        if (isset($_POST['form_name']) && $_POST['form_name'] === $form_name) {
            // Validate form token
            if (isset($_POST['form_token']) && validate_form_token($form_name, $_POST['form_token'])) {
                // Process form
                $result = call_user_func($process_function);

                // Add result to redirect params if it's a boolean
                if (is_bool($result)) {
                    $redirect_params['success'] = $result ? '1' : '0';
                }

                // Redirect to prevent resubmission
                prevent_form_resubmission($redirect_url, $redirect_params);
                return true;
            } else {
                // Invalid token
                set_error_message('Form session expired or invalid. Please try again.');
                return false;
            }
        }
    }

    return false;
}

/**
 * Perform redirect after form processing to prevent resubmission
 * 
 * @param string $redirect_url URL to redirect to
 * @param array $redirect_params Optional parameters to add to redirect URL
 * @return void
 */
function prevent_form_resubmission($redirect_url, $redirect_params = [])
{
    // Build query string from parameters
    $query = '';
    if (!empty($redirect_params)) {
        $query = '?' . http_build_query($redirect_params);
    }

    // Check if headers have already been sent
    if (headers_sent()) {
        // Use JavaScript for redirection if headers already sent
        echo '<script>window.location.href="' . $redirect_url . $query . '";</script>';
        // Fallback for browsers with JavaScript disabled
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . $redirect_url . $query . '"></noscript>';
        exit;
    } else {
        // Redirect to prevent form resubmission
        header("Location: {$redirect_url}{$query}");
        exit;
    }
}

/**
 * Check if a form was successfully processed after redirect
 * 
 * @return bool True if form was successfully processed, false otherwise
 */
function was_form_successful()
{
    return isset($_GET['success']) && $_GET['success'] === '1';
}

/**
 * Generate a unique form ID for the current page
 * 
 * @param string $form_name Base name of the form
 * @return string Unique form ID
 */
function generate_form_id($form_name)
{
    // Generate a unique ID for this page load
    if (!isset($_SESSION['form_ids'][$form_name])) {
        $_SESSION['form_ids'][$form_name] = uniqid($form_name . '_');
    }

    return $_SESSION['form_ids'][$form_name];
}

/**
 * Reset form ID to force a new form on next page load
 * 
 * @param string $form_name Name of the form
 * @return void
 */
function reset_form_id($form_name)
{
    if (isset($_SESSION['form_ids'][$form_name])) {
        unset($_SESSION['form_ids'][$form_name]);
    }
}
