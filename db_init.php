<?php
/**
 * Database Initialization Script
 * Run this ONCE to create all required tables for the inventory system
 * 
 * Access via: http://localhost/invetory_system/db_init.php
 */

$host = "localhost";
$user = "root";
$pass = "";
$db_name = "inventory_db";

// First, create connection without database to create it if it doesn't exist
$temp_conn = mysqli_connect($host, $user, $pass);
if (!$temp_conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Create database if it doesn't exist
$create_db = "CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!mysqli_query($temp_conn, $create_db)) {
    die("Error creating database: " . mysqli_error($temp_conn));
}
mysqli_close($temp_conn);

// Now connect to the database
$conn = mysqli_connect($host, $user, $pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

echo "<h2>Database Initialization</h2>";
echo "<pre>";

// ============================================
// TABLE 1: ITEMS (Main inventory table)
// ============================================
$items_sql = "
CREATE TABLE IF NOT EXISTS `items` (
  `item_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_name` VARCHAR(255) NOT NULL UNIQUE,
  `category` VARCHAR(100) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 0,
  `price` DECIMAL(10, 2) NOT NULL,
  `supplier` VARCHAR(150),
  `image` VARCHAR(255),
  `date_added` DATE NOT NULL,
  `last_modified` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` BOOLEAN DEFAULT TRUE,
  
  INDEX `idx_category` (`category`),
  INDEX `idx_quantity` (`quantity`),
  INDEX `idx_date_added` (`date_added`),
  INDEX `idx_last_modified` (`last_modified`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $items_sql)) {
    echo "✓ Table 'items' created successfully\n";
} else {
    echo "✗ Error creating 'items' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 2: SALES (Track all sales)
// ============================================
$sales_sql = "
CREATE TABLE IF NOT EXISTS `sales` (
  `sale_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `total` DECIMAL(12, 2) NOT NULL,
  `sale_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` TEXT,
  
  FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_sale_date` (`sale_date`),
  INDEX `idx_total` (`total`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $sales_sql)) {
    echo "✓ Table 'sales' created successfully\n";
} else {
    echo "✗ Error creating 'sales' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 3: PURCHASES (Track all purchases)
// ============================================
$purchases_sql = "
CREATE TABLE IF NOT EXISTS `purchases` (
  `purchase_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `quantity` INT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `total` DECIMAL(12, 2) NOT NULL,
  `purchase_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` TEXT,
  
  FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_purchase_date` (`purchase_date`),
  INDEX `idx_total` (`total`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $purchases_sql)) {
    echo "✓ Table 'purchases' created successfully\n";
} else {
    echo "✗ Error creating 'purchases' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 4: ACTIONS (Audit log for all changes)
// ============================================
$actions_sql = "
CREATE TABLE IF NOT EXISTS `actions` (
  `action_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `action_type` ENUM('ADD', 'UPDATE', 'DELETE', 'SALE', 'PURCHASE') NOT NULL,
  `action_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` TEXT NOT NULL,
  
  FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_action_type` (`action_type`),
  INDEX `idx_action_date` (`action_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $actions_sql)) {
    echo "✓ Table 'actions' created successfully\n";
} else {
    echo "✗ Error creating 'actions' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 5: STOCK_HISTORY (Track quantity changes over time)
// ============================================
$stock_history_sql = "
CREATE TABLE IF NOT EXISTS `stock_history` (
  `history_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `previous_quantity` INT NOT NULL,
  `new_quantity` INT NOT NULL,
  `change_type` ENUM('PURCHASE', 'SALE', 'ADJUSTMENT', 'INITIAL') NOT NULL,
  `change_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `notes` TEXT,
  
  FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_change_type` (`change_type`),
  INDEX `idx_change_date` (`change_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $stock_history_sql)) {
    echo "✓ Table 'stock_history' created successfully\n";
} else {
    echo "✗ Error creating 'stock_history' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 6: CATEGORIES (Master list of categories)
// ============================================
$categories_sql = "
CREATE TABLE IF NOT EXISTS `categories` (
  `category_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `category_name` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_category_name` (`category_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $categories_sql)) {
    echo "✓ Table 'categories' created successfully\n";
} else {
    echo "✗ Error creating 'categories' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 7: SUPPLIERS (Master list of suppliers)
// ============================================
$suppliers_sql = "
CREATE TABLE IF NOT EXISTS `suppliers` (
  `supplier_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `supplier_name` VARCHAR(150) NOT NULL UNIQUE,
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `address` TEXT,
  `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_supplier_name` (`supplier_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $suppliers_sql)) {
    echo "✓ Table 'suppliers' created successfully\n";
} else {
    echo "✗ Error creating 'suppliers' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 8: PRICE_CHANGES (Track price history)
// ============================================
$price_changes_sql = "
CREATE TABLE IF NOT EXISTS `price_changes` (
  `price_change_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `old_price` DECIMAL(10, 2) NOT NULL,
  `new_price` DECIMAL(10, 2) NOT NULL,
  `change_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` TEXT,
  
  FOREIGN KEY (`item_id`) REFERENCES `items`(`item_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  INDEX `idx_item_id` (`item_id`),
  INDEX `idx_change_date` (`change_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $price_changes_sql)) {
    echo "✓ Table 'price_changes' created successfully\n";
} else {
    echo "✗ Error creating 'price_changes' table: " . mysqli_error($conn) . "\n";
}

// ============================================
// TABLE 9: DAILY_REPORTS (Cached daily summaries)
// ============================================
$daily_reports_sql = "
CREATE TABLE IF NOT EXISTS `daily_reports` (
  `report_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `report_date` DATE NOT NULL UNIQUE,
  `total_sales` INT NOT NULL DEFAULT 0,
  `total_sales_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `total_purchases` INT NOT NULL DEFAULT 0,
  `total_purchases_amount` DECIMAL(12, 2) NOT NULL DEFAULT 0,
  `generated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  INDEX `idx_report_date` (`report_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

if (mysqli_query($conn, $daily_reports_sql)) {
    echo "✓ Table 'daily_reports' created successfully\n";
} else {
    echo "✗ Error creating 'daily_reports' table: " . mysqli_error($conn) . "\n";
}

echo "\n";
echo "============================================\n";
echo "Database initialization completed!\n";
echo "============================================\n";
echo "All tables have been created successfully.\n";
echo "You can now start using your inventory system.\n";
echo "</pre>";

mysqli_close($conn);
?>
