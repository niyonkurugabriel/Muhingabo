<?php
/**
 * Session Configuration & Initialization
 * Handles all session-related settings and security
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Session configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('DEFAULT_USERNAME', 'dope');
define('DEFAULT_PASSWORD', '@1205');

/**
 * Check if user is logged in
 * @return bool True if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Get current logged-in username
 * @return string Username or empty string
 */
function get_current_user() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : '';
}

/**
 * Get session login time
 * @return int Timestamp of login
 */
function get_login_time() {
    return isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 0;
}

/**
 * Check if session has expired
 * @return bool True if session has expired
 */
function is_session_expired() {
    if (!is_logged_in()) {
        return true;
    }
    
    $current_time = time();
    $login_time = get_login_time();
    $elapsed = $current_time - $login_time;
    
    return $elapsed > SESSION_TIMEOUT;
}

/**
 * Validate credentials
 * @param string $username Username
 * @param string $password Password
 * @return bool True if credentials are valid
 */
function validate_credentials($username, $password) {
    // Default credentials (in production, use hashed passwords and database)
    if ($username === DEFAULT_USERNAME && $password === DEFAULT_PASSWORD) {
        return true;
    }
    return false;
}

/**
 * Create session for user
 * @param string $username Username
 * @return void
 */
function create_session($username) {
    $_SESSION['user_id'] = 1; // Default user ID
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}

/**
 * Destroy session
 * @return void
 */
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

/**
 * Require login - redirect if not logged in
 * @param string $redirect_url URL to redirect to
 * @return void
 */
function require_login($redirect_url = 'login.php') {
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

/**
 * Get user display name
 * @return string Display name
 */
function get_user_display_name() {
    $username = get_current_user();
    return ucfirst($username);
}

?>
