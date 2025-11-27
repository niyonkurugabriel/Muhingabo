# Inventory System Database Structure

## Overview
This database is designed to efficiently manage a hardware shop inventory system with comprehensive tracking of items, sales, purchases, and audit logs.

---

## Database: `inventory_db`

### Tables

#### 1. **items** - Main Inventory Table
Stores all inventory items and their current status.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `item_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique item identifier |
| `item_name` | VARCHAR(255) | NOT NULL, UNIQUE | Product name |
| `category` | VARCHAR(100) | NOT NULL | Product category |
| `quantity` | INT | NOT NULL, DEFAULT 0 | Current stock quantity |
| `price` | DECIMAL(10,2) | NOT NULL | Unit price in FRW |
| `supplier` | VARCHAR(150) | - | Supplier name |
| `image` | VARCHAR(255) | - | Path to item image |
| `date_added` | DATE | NOT NULL | When item was first added |
| `last_modified` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Last update timestamp |
| `is_active` | BOOLEAN | DEFAULT TRUE | Soft delete flag |

**Indexes:** category, quantity, date_added, last_modified
**Usage:** Primary table for all inventory operations

---

#### 2. **sales** - Sales Transactions
Tracks all sales transactions with quantity and pricing.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `sale_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique sale identifier |
| `item_id` | INT | FOREIGN KEY → items | Reference to sold item |
| `quantity` | INT | NOT NULL | Quantity sold |
| `price` | DECIMAL(10,2) | NOT NULL | Sale price per unit |
| `total` | DECIMAL(12,2) | NOT NULL | Total sale amount |
| `sale_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When sale occurred |
| `details` | TEXT | - | Additional sale notes |

**Indexes:** item_id, sale_date, total
**Foreign Keys:** item_id references items(item_id)
**Usage:** Complete sales history and reporting

---

#### 3. **purchases** - Purchase Transactions
Tracks all purchase transactions from suppliers.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `purchase_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique purchase identifier |
| `item_id` | INT | FOREIGN KEY → items | Reference to purchased item |
| `quantity` | INT | NOT NULL | Quantity purchased |
| `price` | DECIMAL(10,2) | NOT NULL | Purchase price per unit |
| `total` | DECIMAL(12,2) | NOT NULL | Total purchase amount |
| `purchase_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When purchase occurred |
| `details` | TEXT | - | Purchase notes |

**Indexes:** item_id, purchase_date, total
**Foreign Keys:** item_id references items(item_id)
**Usage:** Supplier transactions and cost tracking

---

#### 4. **actions** - Audit Log
Complete audit trail of all system actions for compliance and tracking.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `action_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique action identifier |
| `item_id` | INT | FOREIGN KEY → items | Reference to affected item |
| `action_type` | ENUM | NOT NULL | Type: ADD, UPDATE, DELETE, SALE, PURCHASE |
| `action_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When action occurred |
| `details` | TEXT | NOT NULL | Description of action |

**Indexes:** item_id, action_type, action_date
**Foreign Keys:** item_id references items(item_id)
**Usage:** Complete system audit trail and history

---

#### 5. **stock_history** - Stock Changes Over Time
Detailed history of all quantity changes for trend analysis.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `history_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique history record ID |
| `item_id` | INT | FOREIGN KEY → items | Reference to item |
| `previous_quantity` | INT | NOT NULL | Quantity before change |
| `new_quantity` | INT | NOT NULL | Quantity after change |
| `change_type` | ENUM | NOT NULL | Type: PURCHASE, SALE, ADJUSTMENT, INITIAL |
| `change_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When change occurred |
| `notes` | TEXT | - | Change notes |

**Indexes:** item_id, change_type, change_date
**Foreign Keys:** item_id references items(item_id)
**Usage:** Stock level trends and analysis

---

#### 6. **categories** - Category Master List
Centralized list of product categories for better data management.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `category_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique category ID |
| `category_name` | VARCHAR(100) | NOT NULL, UNIQUE | Category name |
| `description` | TEXT | - | Category description |
| `created_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Creation date |

**Indexes:** category_name
**Usage:** Standardized category selection

---

#### 7. **suppliers** - Supplier Master List
Centralized supplier information for purchase orders and management.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `supplier_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique supplier ID |
| `supplier_name` | VARCHAR(150) | NOT NULL, UNIQUE | Supplier name |
| `email` | VARCHAR(100) | - | Contact email |
| `phone` | VARCHAR(20) | - | Contact phone |
| `address` | TEXT | - | Supplier address |
| `created_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When added |

**Indexes:** supplier_name
**Usage:** Supplier contact and purchase management

---

#### 8. **price_changes** - Price History
Track all price modifications for cost analysis and auditing.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `price_change_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique change ID |
| `item_id` | INT | FOREIGN KEY → items | Reference to item |
| `old_price` | DECIMAL(10,2) | NOT NULL | Previous price |
| `new_price` | DECIMAL(10,2) | NOT NULL | Updated price |
| `change_date` | DATETIME | DEFAULT CURRENT_TIMESTAMP | When price changed |
| `reason` | TEXT | - | Reason for price change |

**Indexes:** item_id, change_date
**Foreign Keys:** item_id references items(item_id)
**Usage:** Price trend analysis and history

---

#### 9. **daily_reports** - Daily Summary Cache
Pre-calculated daily summaries for fast reporting and analytics.

| Column | Type | Constraints | Purpose |
|--------|------|-----------|---------|
| `report_id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique report ID |
| `report_date` | DATE | NOT NULL, UNIQUE | Date of report |
| `total_sales` | INT | NOT NULL, DEFAULT 0 | Total sales count |
| `total_sales_amount` | DECIMAL(12,2) | NOT NULL, DEFAULT 0 | Total sales value |
| `total_purchases` | INT | NOT NULL, DEFAULT 0 | Total purchases count |
| `total_purchases_amount` | DECIMAL(12,2) | NOT NULL, DEFAULT 0 | Total purchases value |
| `generated_at` | DATETIME | DEFAULT CURRENT_TIMESTAMP | Report generation time |

**Indexes:** report_date
**Usage:** Fast daily reporting and dashboards

---

## Key Features & Design Decisions

### 1. **Normalization**
- ✓ Properly normalized structure to reduce redundancy
- ✓ Separate master tables (categories, suppliers) for maintainability

### 2. **Performance Optimization**
- ✓ Strategic indexes on frequently queried columns
- ✓ Foreign keys for referential integrity
- ✓ Decimal types for accurate monetary calculations
- ✓ DATETIME with DEFAULT CURRENT_TIMESTAMP for automatic timestamps

### 3. **Data Integrity**
- ✓ Foreign key constraints prevent orphaned records
- ✓ UNIQUE constraints on identifiers
- ✓ NOT NULL constraints on critical fields
- ✓ ENUM types for restricted value sets (action_type, change_type)

### 4. **Audit & Compliance**
- ✓ Complete action logging in `actions` table
- ✓ Stock history tracking for trend analysis
- ✓ Price change history for cost auditing
- ✓ Timestamp tracking on all transactions

### 5. **Scalability**
- ✓ INT AUTO_INCREMENT for efficient primary keys
- ✓ Proper indexing strategy for quick lookups
- ✓ DECIMAL(12,2) for large monetary values
- ✓ TEXT fields for flexible note storage

---

## Setup Instructions

### Step 1: Initialize Database
1. Open your browser and navigate to:
   ```
   http://localhost/invetory_system/db_init.php
   ```
2. The script will create all tables automatically

### Step 2: Verify Connection
- Check `db_connect.php` has correct credentials:
  - Host: localhost
  - User: root
  - Password: (empty by default)
  - Database: inventory_db

### Step 3: Start Using
- Add items via `add_item.php`
- Record sales via `sell_item.php`
- Record purchases via `purchase_item.php`
- View audit logs via `audit_log.php`

---

## Useful Queries for Maintenance

### Get Current Stock Levels
```sql
SELECT item_name, category, quantity, price 
FROM items 
WHERE is_active = TRUE 
ORDER BY quantity ASC;
```

### Low Stock Alert (less than 5 units)
```sql
SELECT item_id, item_name, quantity, supplier 
FROM items 
WHERE quantity < 5 AND is_active = TRUE;
```

### Daily Sales Summary
```sql
SELECT DATE(sale_date) as date, COUNT(*) as total_sales, SUM(total) as revenue
FROM sales
WHERE sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(sale_date)
ORDER BY date DESC;
```

### Top Selling Items (Last 30 Days)
```sql
SELECT i.item_name, COUNT(s.sale_id) as times_sold, SUM(s.quantity) as total_qty, SUM(s.total) as revenue
FROM sales s
JOIN items i ON s.item_id = i.item_id
WHERE s.sale_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY s.item_id
ORDER BY times_sold DESC
LIMIT 10;
```

### Audit Trail for Specific Item
```sql
SELECT action_date, action_type, details
FROM actions
WHERE item_id = ? 
ORDER BY action_date DESC;
```

---

## Best Practices

1. **Always use prepared statements** for security (already implemented in db_connect.php)
2. **Use transactions** for multi-step operations (sales/purchases already do this)
3. **Validate input** before database operations
4. **Index strategically** - indexes are already optimized
5. **Regular backups** - backup database regularly
6. **Monitor performance** - use slow query log if needed
7. **Use helper functions** - leverage functions in db_connect.php

---

## Connection Helper Functions

### Basic Usage
```php
// Include the database connection
include 'db_connect.php';

// Execute a query
$result = execute_query("SELECT * FROM items");

// Get a single row
$item = get_row("SELECT * FROM items WHERE item_id = 1");

// Get all rows
$items = get_rows("SELECT * FROM items");

// Log an action
log_action(1, 'UPDATE', 'Price updated from 1000 to 1200');

// Format currency
echo currency(1500.50);  // Output: FRW 1,500.50
```

---

## Support & Troubleshooting

If tables don't create:
1. Ensure MySQL is running
2. Check credentials in `db_connect.php`
3. Ensure database `inventory_db` exists or let the script create it
4. Check file permissions in the `logs` directory

For connection issues:
- Check `logs/tx_debug.log` for detailed error messages
- Verify MySQL user `root` exists and has no password (or update credentials)
- Ensure UTF-8 charset is supported

---

**Last Updated:** November 27, 2025
**Version:** 1.0
