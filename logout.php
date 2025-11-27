<?php
/**
 * Logout Page
 * Handles user logout and session destruction
 */

include 'session_config.php';

// Destroy the session
destroy_session();

// Redirect to login
header('Location: login.php?logout=success');
exit;
?>
