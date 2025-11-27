<?php
/**
 * Database Connection & Configuration
 * Efficient connection with error handling and helper functions
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventory_db');

// Establish connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    // Log error to file instead of displaying
    error_log("Database Connection Failed: " . mysqli_connect_error());
    die("Database connection failed. Please contact the system administrator.");
}

// Set charset to UTF-8 for proper encoding
mysqli_set_charset($conn, "utf8mb4");

// Enable error reporting for development (comment out in production)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Format currency with FRW prefix
 * @param mixed $amount The amount to format
 * @param int $decimals Number of decimal places
 * @return string Formatted currency string
 */
if (!function_exists('currency')) {
    function currency($amount, $decimals = 2) {
        $n = is_numeric($amount) ? (float)$amount : 0;
        return 'FRW ' . number_format($n, $decimals);
    }
}

/**
 * Safely execute a query and return results
 * @param string $query SQL query
 * @return mysqli_result|bool Query result or false on failure
 */
function execute_query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if (!$result) {
        error_log("Query Error: " . mysqli_error($conn) . " | Query: " . $query);
    }
    return $result;
}

/**
 * Get a single row result
 * @param string $query SQL query
 * @return array|null Single row as associative array or null if not found
 */
function get_row($query) {
    $result = execute_query($query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}

/**
 * Get all rows from a query result
 * @param string $query SQL query
 * @return array Array of rows
 */
function get_rows($query) {
    $rows = [];
    $result = execute_query($query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }
    }
    return $rows;
}

/**
 * Safely escape string for database
 * @param string $string String to escape
 * @return string Escaped string
 */
function safe_string($string) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($string));
}

/**
 * Log an action to the audit log
 * @param int $item_id Item ID
 * @param string $action_type Action type (ADD, UPDATE, DELETE, SALE, PURCHASE)
 * @param string $details Details of the action
 * @return bool Success status
 */
function log_action($item_id, $action_type, $details) {
    global $conn;
    $item_id = (int)$item_id;
    $details = safe_string($details);
    
    $sql = "INSERT INTO actions (item_id, action_type, action_date, details) 
            VALUES ($item_id, '$action_type', NOW(), '$details')";
    return (bool)execute_query($sql);
}

/**
 * Get database connection object
 * @return mysqli Database connection
 */
function get_db_connection() {
    global $conn;
    return $conn;
}

?>

