<?php
/**
 * System Configuration File
 * Centralized settings for the inventory system
 * 
 * COPY THIS FILE TO config.php AND CUSTOMIZE AS NEEDED
 */

// ============================================
// DATABASE CONFIGURATION
// ============================================

// Database connection settings
const DB_HOST = 'localhost';
const DB_USER = 'root';
const DB_PASS = '';
const DB_NAME = 'inventory_db';
const DB_CHARSET = 'utf8mb4';

// ============================================
// APPLICATION SETTINGS
// ============================================

// Shop/Business Information
const SHOP_NAME = 'MUHINGABO Hardware Shop';
const SHOP_EMAIL = 'info@muhingabo.com';
const SHOP_PHONE = '+250 (0) 789 123 456';
const SHOP_ADDRESS = 'Kigali, Rwanda';
const CURRENCY = 'FRW';
const CURRENCY_SYMBOL = 'FRW ';

// Site URL (for links and redirects)
const SITE_URL = 'http://localhost/invetory_system/';
const UPLOAD_DIR = __DIR__ . '/uploads/';
const UPLOAD_URL = 'uploads/';
const LOG_DIR = __DIR__ . '/logs/';

// ============================================
// FEATURE FLAGS
// ============================================

// Enable/disable features
const ENABLE_IMAGES = true;           // Allow product images
const ENABLE_SUPPLIERS = true;        // Track suppliers
const ENABLE_AUDIT_LOG = true;        // Keep audit trail
const ENABLE_STOCK_HISTORY = true;    // Track stock changes
const ENABLE_PRICE_TRACKING = true;   // Track price changes
const ENABLE_DAILY_REPORTS = true;    // Generate daily reports

// ============================================
// BUSINESS RULES
// ============================================

// Low stock alert threshold
const LOW_STOCK_THRESHOLD = 5;

// Maximum file upload size (in bytes)
const MAX_UPLOAD_SIZE = 5 * 1024 * 1024; // 5MB

// Allowed image types
const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

// Date format for display
const DATE_FORMAT = 'Y-m-d';
const DATETIME_FORMAT = 'Y-m-d H:i:s';
const DISPLAY_DATE_FORMAT = 'd/m/Y';
const DISPLAY_DATETIME_FORMAT = 'd/m/Y H:i';

// ============================================
// PAGINATION
// ============================================

const ITEMS_PER_PAGE = 20;
const SEARCH_RESULTS_PER_PAGE = 50;

// ============================================
// DECIMAL PRECISION
// ============================================

// Number of decimal places for currency
const CURRENCY_DECIMALS = 2;

// ============================================
// SYSTEM MAINTENANCE
// ============================================

// Enable debug mode (set to false in production)
const DEBUG_MODE = false;

// Log queries for debugging
const LOG_QUERIES = false;

// Session timeout (in minutes)
const SESSION_TIMEOUT = 30;

// ============================================
// EMAIL NOTIFICATIONS (Future Enhancement)
// ============================================

// Email notifications for low stock
const SEND_LOW_STOCK_EMAIL = false;
const LOW_STOCK_EMAIL = 'admin@muhingabo.com';

// ============================================
// BACKUP SETTINGS (Future Enhancement)
// ============================================

// Automatic backup schedule
const AUTO_BACKUP_ENABLED = false;
const AUTO_BACKUP_INTERVAL = 'weekly'; // daily, weekly, monthly
const BACKUP_DIR = __DIR__ . '/backups/';

// ============================================
// HELPER FUNCTIONS FOR CONFIG
// ============================================

/**
 * Get configuration value with fallback
 * @param string $key Configuration key
 * @param mixed $default Default value if not found
 * @return mixed Configuration value
 */
function config($key, $default = null) {
    if (defined($key)) {
        return constant($key);
    }
    return $default;
}

/**
 * Check if feature is enabled
 * @param string $feature Feature name
 * @return bool True if enabled
 */
function feature_enabled($feature) {
    $feature_key = 'ENABLE_' . strtoupper($feature);
    return defined($feature_key) && constant($feature_key) === true;
}

/**
 * Get upload directory path
 * @return string Upload directory path
 */
function get_upload_dir() {
    return config('UPLOAD_DIR', __DIR__ . '/uploads/');
}

/**
 * Get log directory path
 * @return string Log directory path
 */
function get_log_dir() {
    return config('LOG_DIR', __DIR__ . '/logs/');
}

/**
 * Format date according to config
 * @param string $date Date string
 * @param bool $with_time Include time
 * @return string Formatted date
 */
function format_date($date, $with_time = false) {
    if (empty($date)) return '-';
    $format = $with_time ? config('DISPLAY_DATETIME_FORMAT') : config('DISPLAY_DATE_FORMAT');
    return date($format, strtotime($date));
}

/**
 * Format currency according to config
 * @param float $amount Amount to format
 * @return string Formatted currency
 */
function format_currency($amount) {
    return config('CURRENCY_SYMBOL') . number_format($amount, config('CURRENCY_DECIMALS'));
}

?>
