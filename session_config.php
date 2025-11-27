<?php
/**
 * Session Configuration & Initialization
 * Handles all session-related settings and security
 */

// Prevent multiple inclusion / redeclaration
if (defined('SESSION_CONFIG_LOADED')) {
    return;
}
define('SESSION_CONFIG_LOADED', true);

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('DEFAULT_USERNAME', 'dope');
define('DEFAULT_PASSWORD', '@1205');

// Configure session cookie to be a browser-session cookie (expire on close)
$cookieParams = [
    'lifetime' => 0,
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
];

if (PHP_SAPI !== 'cli') {
    // Set session name to a custom name to avoid collisions
    session_name('muhingabo_sess');
    // Apply cookie params before starting session
    session_set_cookie_params($cookieParams);

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 * @return bool True if user is logged in
 */
if (!function_exists('is_logged_in')) {
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}
}

/**
 * Get current logged-in username
 * @return string Username or empty string
 */
if (!function_exists('get_current_user')) {
function get_current_user() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : '';
}
}

/**
 * Get session login time
 * @return int Timestamp of login
 */
if (!function_exists('get_login_time')) {
function get_login_time() {
    return isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0;
}
}

/**
 * Check if session has expired
 * @return bool True if session has expired
 */
if (!function_exists('is_session_expired')) {
function is_session_expired() {
    if (!is_logged_in()) {
        return true;
    }
    // Use last activity timestamp for inactivity expiration
    $current_time = time();
    $last = isset($_SESSION['last_activity']) ? (int) $_SESSION['last_activity'] : 0;
    $elapsed = $current_time - $last;

    return $elapsed > SESSION_TIMEOUT;
}
}

/**
 * Validate credentials
 * @param string $username Username
 * @param string $password Password
 * @return bool True if credentials are valid
 */
if (!function_exists('validate_credentials')) {
function validate_credentials($username, $password) {
    // Default credentials (in production, use hashed passwords and database)
    if ($username === DEFAULT_USERNAME && $password === DEFAULT_PASSWORD) {
        return true;
    }
    return false;
}
}

/**
 * Create session for user
 * @param string $username Username
 * @return void
 */
if (!function_exists('create_session')) {
function create_session($username) {
    // Regenerate session id on login to prevent fixation
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }

    $_SESSION['user_id'] = 1; // Default user ID
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}
}

/**
 * Destroy session
 * @return void
 */
if (!function_exists('destroy_session')) {
function destroy_session() {
    $_SESSION = array();
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}
}

/**
 * Require login - redirect if not logged in
 * @param string $redirect_url URL to redirect to
 * @return void
 */
if (!function_exists('require_login')) {
function require_login($redirect_url = 'index.php') {
    if (!is_logged_in() || is_session_expired()) {
        // Clear expired session
        destroy_session();
        
        // Redirect to login
        header("Location: $redirect_url");
        exit;
    }
    
    // Update last activity time
    $_SESSION['last_activity'] = time();
}
}

/**
 * Get user display name
 * @return string Display name
 */
if (!function_exists('get_user_display_name')) {
function get_user_display_name() {
    $username = get_current_user();
    return ucfirst($username);
}
}

?>
