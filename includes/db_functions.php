<?php
/**
 * Database Functions
 *
 * This file contains secure database functions to prevent SQL injection.
 */

/**
 * Prepare and execute a SQL query with parameters
 *
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Parameters to bind
 * @return mysqli_stmt|false Prepared statement or false on failure
 */
function db_query($query, $types = '', $params = [])
{
    global $conn;
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $query);
    
    if (!$stmt) {
        error_log("Query preparation failed: " . mysqli_error($conn));
        return false;
    }
    
    // Bind parameters if provided
    if (!empty($params) && !empty($types)) {
        // Create array with references to parameters
        $bind_params = array();
        $bind_params[] = &$types;
        
        for ($i = 0; $i < count($params); $i++) {
            $bind_params[] = &$params[$i];
        }
        
        // Call mysqli_stmt_bind_param with dynamic parameters
        call_user_func_array(array($stmt, 'bind_param'), $bind_params);
    }
    
    // Execute statement
    if (!mysqli_stmt_execute($stmt)) {
        error_log("Query execution failed: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
        return false;
    }
    
    return $stmt;
}

/**
 * Execute a SELECT query and return all results
 *
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Parameters to bind
 * @return array|false Array of results or false on failure
 */
function db_select($query, $types = '', $params = [])
{
    $stmt = db_query($query, $types, $params);
    
    if (!$stmt) {
        return false;
    }
    
    // Get result
    $result = mysqli_stmt_get_result($stmt);
    
    if (!$result) {
        mysqli_stmt_close($stmt);
        return false;
    }
    
    // Fetch all rows
    $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
    
    // Free result and close statement
    mysqli_free_result($result);
    mysqli_stmt_close($stmt);
    
    return $rows;
}

/**
 * Execute a SELECT query and return a single row
 *
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Parameters to bind
 * @return array|null|false Array with row data, null if no results, or false on failure
 */
function db_select_one($query, $types = '', $params = [])
{
    $rows = db_select($query, $types, $params);
    
    if ($rows === false) {
        return false;
    }
    
    return empty($rows) ? null : $rows[0];
}

/**
 * Execute an INSERT, UPDATE, or DELETE query
 *
 * @param string $query SQL query with placeholders
 * @param string $types Parameter types (i: integer, d: double, s: string, b: blob)
 * @param array $params Parameters to bind
 * @return int|false Number of affected rows or false on failure
 */
function db_execute($query, $types = '', $params = [])
{
    $stmt = db_query($query, $types, $params);
    
    if (!$stmt) {
        return false;
    }
    
    // Get number of affected rows
    $affected_rows = mysqli_stmt_affected_rows($stmt);
    
    // Close statement
    mysqli_stmt_close($stmt);
    
    return $affected_rows;
}

/**
 * Get the ID of the last inserted row
 *
 * @return int|false Last insert ID or false on failure
 */
function db_last_insert_id()
{
    global $conn;
    return mysqli_insert_id($conn);
}

/**
 * Escape a string for use in a SQL query
 * Note: This should only be used when prepared statements are not possible
 *
 * @param string $string String to escape
 * @return string Escaped string
 */
function db_escape($string)
{
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

/**
 * Begin a transaction
 *
 * @return bool True on success, false on failure
 */
function db_begin_transaction()
{
    global $conn;
    return mysqli_begin_transaction($conn);
}

/**
 * Commit a transaction
 *
 * @return bool True on success, false on failure
 */
function db_commit()
{
    global $conn;
    return mysqli_commit($conn);
}

/**
 * Rollback a transaction
 *
 * @return bool True on success, false on failure
 */
function db_rollback()
{
    global $conn;
    return mysqli_rollback($conn);
}
