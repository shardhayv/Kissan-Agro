<?php
/**
 * Visitor Tracker
 * 
 * This file contains functions for tracking site visitors.
 */

/**
 * Track visitor
 * 
 * @return void
 */
function track_visitor()
{
    global $conn;
    
    // Skip tracking for admin pages and AJAX requests
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false || 
        (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')) {
        return;
    }
    
    // Get visitor information
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    $page_url = $_SERVER['REQUEST_URI'];
    $referrer_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $session_id = session_id();
    
    // Extract browser, OS, and device information from user agent
    $browser = get_browser_name($user_agent);
    $os = get_os_name($user_agent);
    $device_type = get_device_type($user_agent);
    
    // Attempt to get country and city (simplified version)
    $country = '';
    $city = '';
    
    // Insert visitor log
    $query = "INSERT INTO visitor_logs (ip_address, user_agent, page_url, referrer_url, session_id, country, city, browser, os, device_type)
              VALUES ('$ip_address', '$user_agent', '$page_url', '$referrer_url', '$session_id', '$country', '$city', '$browser', '$os', '$device_type')";
    
    mysqli_query($conn, $query);
}

/**
 * Get browser name from user agent
 * 
 * @param string $user_agent User agent string
 * @return string Browser name
 */
function get_browser_name($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) {
        return 'Opera';
    } elseif (strpos($user_agent, 'Edge')) {
        return 'Edge';
    } elseif (strpos($user_agent, 'Chrome')) {
        return 'Chrome';
    } elseif (strpos($user_agent, 'Safari')) {
        return 'Safari';
    } elseif (strpos($user_agent, 'Firefox')) {
        return 'Firefox';
    } elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) {
        return 'Internet Explorer';
    }
    
    return 'Unknown';
}

/**
 * Get OS name from user agent
 * 
 * @param string $user_agent User agent string
 * @return string OS name
 */
function get_os_name($user_agent)
{
    if (strpos($user_agent, 'Windows NT 10.0')) {
        return 'Windows 10';
    } elseif (strpos($user_agent, 'Windows NT 6.3')) {
        return 'Windows 8.1';
    } elseif (strpos($user_agent, 'Windows NT 6.2')) {
        return 'Windows 8';
    } elseif (strpos($user_agent, 'Windows NT 6.1')) {
        return 'Windows 7';
    } elseif (strpos($user_agent, 'Windows NT 6.0')) {
        return 'Windows Vista';
    } elseif (strpos($user_agent, 'Windows NT 5.1')) {
        return 'Windows XP';
    } elseif (strpos($user_agent, 'Windows NT 5.0')) {
        return 'Windows 2000';
    } elseif (strpos($user_agent, 'Mac')) {
        return 'Mac OS';
    } elseif (strpos($user_agent, 'X11')) {
        return 'UNIX';
    } elseif (strpos($user_agent, 'Linux')) {
        return 'Linux';
    } elseif (strpos($user_agent, 'Android')) {
        return 'Android';
    } elseif (strpos($user_agent, 'iPhone') || strpos($user_agent, 'iPad')) {
        return 'iOS';
    }
    
    return 'Unknown';
}

/**
 * Get device type from user agent
 * 
 * @param string $user_agent User agent string
 * @return string Device type
 */
function get_device_type($user_agent)
{
    if (strpos($user_agent, 'Mobile') !== false || strpos($user_agent, 'Android') !== false || strpos($user_agent, 'iPhone') !== false) {
        return 'Mobile';
    } elseif (strpos($user_agent, 'Tablet') !== false || strpos($user_agent, 'iPad') !== false) {
        return 'Tablet';
    } else {
        return 'Desktop';
    }
}
