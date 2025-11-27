# 🚀 Database Setup & Configuration Guide

## Quick Start (5 Minutes)

### Step 1: Open Database Initialization
1. Make sure XAMPP is running (MySQL module enabled)
2. Open your browser and go to:
   ```
   http://localhost/invetory_system/db_init.php
   ```
3. Wait for the success message - all tables will be created automatically

### Step 2: Verify Setup
1. Navigate to:
   ```
   http://localhost/invetory_system/db_verify.php
   ```
2. Check that all tables show "✓ Exists"
3. You should see statistics dashboard with 0 items initially

### Step 3: Start Using the System
- Go to: `http://localhost/invetory_system/`
- Add your first item using "Add Item"
- Record sales/purchases as needed

---

## What's New? 

### 📊 Enhanced Database Structure
We've created a professional, scalable database with **9 optimized tables**:

| Table | Purpose |
|-------|---------|
| `items` | Main inventory stock |
| `sales` | All sales transactions |
| `purchases` | All purchase transactions |
| `actions` | Complete audit trail |
| `stock_history` | Quantity change history |
| `categories` | Product categories master list |
| `suppliers` | Supplier information master list |
| `price_changes` | Price history tracking |
| `daily_reports` | Daily summary cache |

### 🔐 Security Improvements
- ✓ Prepared statements for SQL injection prevention
- ✓ Input sanitization helpers
- ✓ Error logging instead of displaying errors
- ✓ Foreign key constraints for data integrity
- ✓ UTF-8 charset for international support

### ⚡ Performance Enhancements
- ✓ Strategic indexing on frequently queried columns
- ✓ Decimal types for accurate calculations
- ✓ Automatic timestamps
- ✓ Soft delete support (is_active flag)
- ✓ Query helper functions for consistency

---

## Key Files Created/Updated

### 1. `db_init.php` - Database Initialization
**What it does:** Creates all 9 tables with proper structure
**Access:** `http://localhost/invetory_system/db_init.php`
**Usage:** Run ONCE on first setup or to reset database

### 2. `db_connect.php` - Enhanced Connection & Helpers
**Updated with:**
- Better error handling
- 6 new helper functions
- Secure practices
- UTF-8 support

**Available Functions:**
```php
execute_query($query)          // Execute any query safely
get_row($query)                // Get single result
get_rows($query)               // Get multiple results
safe_string($string)           // Sanitize input
log_action($id, $type, $msg)   // Log audit entry
currency($amount)              // Format currency
```

### 3. `db_verify.php` - Status & Statistics Dashboard
**What it shows:**
- ✓ Table existence check
- 📊 Live statistics
- ⚠️ Low stock alerts
- 📈 Today's summary
- 🔗 Quick navigation links

**Access:** `http://localhost/invetory_system/db_verify.php`

### 4. `DATABASE_STRUCTURE.md` - Complete Documentation
**Contains:**
- Detailed table schemas
- Design decisions
- Useful queries
- Best practices
- Troubleshooting tips

---

## Database Connection Details

### Current Configuration (db_connect.php)
```
Host:     localhost
User:     root
Password: (empty)
Database: inventory_db
Charset:  utf8mb4
```

### If You Need to Change Credentials
Edit `db_connect.php` lines 8-11:
```php
define('DB_HOST', 'localhost');  // Change host
define('DB_USER', 'root');       // Change username
define('DB_PASS', '');           // Add password if needed
define('DB_NAME', 'inventory_db');  // Change db name
```

---

## Using the Helper Functions

### Example: Add Item with Audit Log
```php
<?php
include 'db_connect.php';

// Insert item
$sql = "INSERT INTO items (item_name, category, quantity, price, supplier, date_added, last_modified) 
        VALUES ('Hammer', 'Tools', 50, 5000, 'ABC Supply', NOW(), NOW())";
execute_query($sql);

// Get the item ID
$item = get_row("SELECT item_id FROM items WHERE item_name = 'Hammer'");
$item_id = $item['item_id'];

// Log the action
log_action($item_id, 'ADD', 'Added 50 units of Hammer from ABC Supply');

echo "Item added successfully!";
?>
```

### Example: Get All Items with Low Stock
```php
<?php
include 'db_connect.php';

$low_stock = get_rows("
    SELECT item_name, quantity, supplier 
    FROM items 
    WHERE quantity < 5 AND is_active = TRUE
    ORDER BY quantity ASC
");

foreach ($low_stock as $item) {
    echo $item['item_name'] . ": " . $item['quantity'] . " units<br>";
}
?>
```

### Example: Generate Daily Report
```php
<?php
include 'db_connect.php';

$today = date('Y-m-d');

$sales = get_rows("
    SELECT COUNT(*) as count, SUM(total) as amount 
    FROM sales 
    WHERE DATE(sale_date) = '$today'
");

echo "Today's Sales: " . $sales[0]['count'] . " transactions";
echo "Revenue: " . currency($sales[0]['amount']);
?>
```

---

## Common Operations

### ✅ Add a New Item
```php
// In add_item.php (already implemented)
INSERT INTO items (item_name, category, quantity, price, supplier, date_added, last_modified, image)
VALUES ('Item Name', 'Category', quantity, price, 'Supplier', NOW(), NOW(), 'image_path');

// Then log it
log_action($item_id, 'ADD', "Added item: Item Name (qty: quantity)");
```

### ✅ Record a Sale
```php
// In save_sale.php (already implemented with transactions)
// 1. Insert sale record
INSERT INTO sales (item_id, quantity, price, total, sale_date) 
VALUES (id, qty, price, total, NOW());

// 2. Update stock
UPDATE items SET quantity = quantity - qty WHERE item_id = id;

// 3. Log action
INSERT INTO actions (item_id, action_type, action_date, details)
VALUES (id, 'SALE', NOW(), 'Sale: qty x item at price');
```

### ✅ Record a Purchase
```php
// Similar to sale but adds to quantity instead
// 1. Insert purchase record
INSERT INTO purchases (item_id, quantity, price, total, purchase_date)
VALUES (id, qty, price, total, NOW());

// 2. Update stock (ADD instead of subtract)
UPDATE items SET quantity = quantity + qty WHERE item_id = id;

// 3. Log action
INSERT INTO actions (item_id, action_type, action_date, details)
VALUES (id, 'PURCHASE', NOW(), 'Purchase: qty x item at price');
```

---

## Useful Database Queries

### 📊 Reports & Analytics

**Total Inventory Value:**
```sql
SELECT SUM(quantity * price) as inventory_value FROM items WHERE is_active = TRUE;
```

**Best Selling Items (Last 30 Days):**
```sql
SELECT i.item_name, SUM(s.quantity) as total_sold, SUM(s.total) as revenue
FROM sales s
JOIN items i ON s.item_id = i.item_id
WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY s.item_id
ORDER BY total_sold DESC
LIMIT 10;
```

**Stock Movement History:**
```sql
SELECT i.item_name, sh.change_type, sh.previous_quantity, sh.new_quantity, sh.change_date
FROM stock_history sh
JOIN items i ON sh.item_id = i.item_id
WHERE sh.item_id = ? 
ORDER BY sh.change_date DESC
LIMIT 50;
```

**Audit Trail for Specific Item:**
```sql
SELECT * FROM actions WHERE item_id = ? ORDER BY action_date DESC;
```

**Monthly Sales Summary:**
```sql
SELECT DATE_TRUNC('month', sale_date) as month, COUNT(*) as transactions, SUM(total) as revenue
FROM sales
GROUP BY DATE_TRUNC('month', sale_date)
ORDER BY month DESC;
```

---

## Backup & Maintenance

### Backup Database
```bash
mysqldump -u root inventory_db > backup_inventory.sql
```

### Restore Database
```bash
mysql -u root inventory_db < backup_inventory.sql
```

### Check Table Status
```sql
CHECK TABLE items, sales, purchases, actions, stock_history, categories, suppliers, price_changes, daily_reports;
```

---

## Troubleshooting

### ❌ Database Connection Failed
**Solution:**
1. Check MySQL is running in XAMPP Control Panel
2. Verify credentials in `db_connect.php`
3. Check if database exists in phpMyAdmin

### ❌ Tables Don't Exist
**Solution:**
1. Run `db_init.php` again
2. Check error log in browser
3. Verify MySQL user has CREATE TABLE privileges

### ❌ Duplicate Entry Error
**Solution:**
- Item names are UNIQUE (case-insensitive)
- Change the item name or delete the old one first
- Check `item_name` field

### ❌ Foreign Key Constraint Error
**Solution:**
- Don't delete items with sales/purchases records
- Use soft delete (set `is_active = FALSE`)
- Check referential integrity constraints

### ❌ Permission Denied on File Upload
**Solution:**
1. Create `/uploads` directory if missing
2. Set permissions: `chmod 755 uploads` on Linux/Mac
3. Ensure `logs` directory exists with write permissions

---

## Performance Tips

### 🚀 Optimize Queries
1. Always use indexes on frequently searched columns ✓ (Already done)
2. Use LIMIT to restrict results
3. Avoid SELECT * when possible
4. Use transactions for multi-step operations ✓ (Already done)

### 💾 Database Maintenance
1. Regular backups (daily/weekly)
2. Archive old sales data monthly
3. Rebuild indexes periodically
4. Monitor disk space

### 🔒 Security
1. Keep MySQL updated
2. Use strong passwords
3. Restrict database user privileges
4. Enable query logging for auditing
5. Regular security backups

---

## Next Steps

1. ✅ Run `db_init.php` to create tables
2. ✅ Open `db_verify.php` to verify setup
3. ✅ Start adding items via `add_item.php`
4. ✅ Record sales/purchases
5. ✅ View audit logs and reports
6. 📖 Read `DATABASE_STRUCTURE.md` for more details

---

## Support Resources

- **Database Documentation:** `DATABASE_STRUCTURE.md`
- **Verification Dashboard:** `db_verify.php`
- **PHP MyAdmin:** `http://localhost/phpmyadmin`
- **Error Logs:** `logs/tx_debug.log`

---

**Created:** November 27, 2025  
**System:** Muhingabo Hardware Inventory  
**Version:** 1.0
