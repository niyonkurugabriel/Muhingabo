<?php
/**
 * Database Status & Verification Tool
 * Shows current database structure and data statistics
 * 
 * Access via: http://localhost/invetory_system/db_verify.php
 */

include 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Verification - Inventory System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .card { margin-bottom: 20px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-box { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; text-align: center; }
        .stat-number { font-size: 32px; font-weight: bold; }
        .stat-label { font-size: 14px; margin-top: 10px; }
        .table-responsive { margin-top: 20px; }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .warning { color: #ffc107; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h2 class="mb-0">📊 Database Verification & Statistics</h2>
            </div>
            <div class="card-body">
                <?php
                    // Check all required tables
                    $tables = ['items', 'sales', 'purchases', 'actions', 'stock_history', 'categories', 'suppliers', 'price_changes', 'daily_reports'];
                    $tables_info = [];
                    $all_exist = true;

                    echo "<h4 class='mb-3'>Table Status</h4>";
                    echo "<table class='table table-striped'>";
                    echo "<thead><tr><th>Table Name</th><th>Status</th><th>Row Count</th><th>Size</th></tr></thead><tbody>";

                    foreach ($tables as $table) {
                        $check = execute_query("SHOW TABLES LIKE '$table'");
                        $exists = mysqli_num_rows($check) > 0;
                        
                        if ($exists) {
                            $count_result = execute_query("SELECT COUNT(*) as cnt FROM $table");
                            $count = mysqli_fetch_assoc($count_result)['cnt'];
                            
                            $size_result = execute_query("SELECT (data_length + index_length) as size FROM information_schema.tables WHERE table_name = '$table' AND table_schema = '".DB_NAME."'");
                            $size_row = mysqli_fetch_assoc($size_result);
                            $size = isset($size_row['size']) ? round($size_row['size'] / 1024, 2) . ' KB' : 'N/A';
                            
                            echo "<tr>";
                            echo "<td><strong>$table</strong></td>";
                            echo "<td><span class='success'>✓ Exists</span></td>";
                            echo "<td>$count</td>";
                            echo "<td>$size</td>";
                            echo "</tr>";
                        } else {
                            echo "<tr>";
                            echo "<td><strong>$table</strong></td>";
                            echo "<td><span class='error'>✗ Missing</span></td>";
                            echo "<td colspan='2'>-</td>";
                            echo "</tr>";
                            $all_exist = false;
                        }
                    }
                    echo "</tbody></table>";

                    if ($all_exist) {
                        echo "<div class='alert alert-success mt-3'><strong>✓ All tables exist!</strong> Your database is properly set up.</div>";
                    } else {
                        echo "<div class='alert alert-danger mt-3'><strong>✗ Missing tables detected!</strong> Please run <a href='db_init.php' class='alert-link'>db_init.php</a> to create them.</div>";
                    }
                ?>
            </div>
        </div>

        <?php if ($all_exist): ?>
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">📈 Statistics Dashboard</h4>
            </div>
            <div class="card-body">
                <div class="stats-grid">
                    <?php
                        // Get statistics
                        $items_count = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM items WHERE is_active = TRUE"))['cnt'];
                        $total_quantity = (int) mysqli_fetch_assoc(execute_query("SELECT SUM(quantity) as total FROM items WHERE is_active = TRUE"))['total'];
                        $sales_count = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM sales"))['cnt'];
                        $purchases_count = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM purchases"))['cnt'];
                        $total_sales_value = mysqli_fetch_assoc(execute_query("SELECT SUM(total) as total FROM sales"))['total'] ?? 0;
                        $total_purchase_value = mysqli_fetch_assoc(execute_query("SELECT SUM(total) as total FROM purchases"))['total'] ?? 0;
                        $actions_count = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM actions"))['cnt'];
                        $low_stock = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM items WHERE quantity < 5 AND is_active = TRUE"))['cnt'];
                    ?>

                    <div class="stat-box">
                        <div class="stat-number"><?php echo $items_count; ?></div>
                        <div class="stat-label">Active Items</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="stat-number"><?php echo number_format($total_quantity); ?></div>
                        <div class="stat-label">Total Units in Stock</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                        <div class="stat-number"><?php echo $sales_count; ?></div>
                        <div class="stat-label">Total Sales</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                        <div class="stat-number"><?php echo $purchases_count; ?></div>
                        <div class="stat-label">Total Purchases</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                        <div class="stat-number"><?php echo currency($total_sales_value); ?></div>
                        <div class="stat-label">Sales Revenue</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #30cfd0 0%, #330867 100%);">
                        <div class="stat-number"><?php echo currency($total_purchase_value); ?></div>
                        <div class="stat-label">Purchase Costs</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #333;">
                        <div class="stat-number"><?php echo $actions_count; ?></div>
                        <div class="stat-label">Audit Actions</div>
                    </div>

                    <div class="stat-box" style="background: linear-gradient(135deg, #ff9a56 0%, #ff6a88 100%);">
                        <div class="stat-number"><?php echo $low_stock; ?></div>
                        <div class="stat-label">Low Stock Items</div>
                    </div>
                </div>

                <!-- Today's Summary -->
                <div style="margin-top: 30px;">
                    <h5>Today's Summary</h5>
                    <?php
                        $today = date('Y-m-d');
                        $today_sales = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM sales WHERE DATE(sale_date) = '$today'"))['cnt'];
                        $today_sales_value = mysqli_fetch_assoc(execute_query("SELECT SUM(total) as total FROM sales WHERE DATE(sale_date) = '$today'"))['total'] ?? 0;
                        $today_purchases = (int) mysqli_fetch_assoc(execute_query("SELECT COUNT(*) as cnt FROM purchases WHERE DATE(purchase_date) = '$today'"))['cnt'];
                        $today_purchases_value = mysqli_fetch_assoc(execute_query("SELECT SUM(total) as total FROM purchases WHERE DATE(purchase_date) = '$today'"))['total'] ?? 0;
                    ?>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Sales Today:</strong></td>
                            <td><?php echo $today_sales; ?> transactions - <?php echo currency($today_sales_value); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Purchases Today:</strong></td>
                            <td><?php echo $today_purchases; ?> transactions - <?php echo currency($today_purchases_value); ?></td>
                        </tr>
                    </table>
                </div>

                <!-- Low Stock Items -->
                <div style="margin-top: 30px;">
                    <h5>⚠️ Low Stock Alert (Less than 5 units)</h5>
                    <?php
                        $low_stock_items = get_rows("SELECT item_id, item_name, quantity, supplier FROM items WHERE quantity < 5 AND is_active = TRUE ORDER BY quantity ASC");
                        if (count($low_stock_items) > 0) {
                            echo "<table class='table table-sm table-danger'>";
                            echo "<thead><tr><th>Item Name</th><th>Qty</th><th>Supplier</th></tr></thead><tbody>";
                            foreach ($low_stock_items as $item) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($item['item_name']) . "</td>";
                                echo "<td><strong>" . $item['quantity'] . "</strong></td>";
                                echo "<td>" . htmlspecialchars($item['supplier']) . "</td>";
                                echo "</tr>";
                            }
                            echo "</tbody></table>";
                        } else {
                            echo "<div class='alert alert-info'>✓ All items are well stocked!</div>";
                        }
                    ?>
                </div>

                <!-- Connection Info -->
                <div style="margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                    <h5>📋 Connection Information</h5>
                    <table class="table table-sm mb-0">
                        <tr>
                            <td><strong>Host:</strong></td>
                            <td><?php echo DB_HOST; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td><?php echo DB_NAME; ?></td>
                        </tr>
                        <tr>
                            <td><strong>User:</strong></td>
                            <td><?php echo DB_USER; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Charset:</strong></td>
                            <td>utf8mb4</td>
                        </tr>
                        <tr>
                            <td><strong>MySQL Version:</strong></td>
                            <td><?php echo mysqli_get_server_info($conn); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">🔗 Quick Links</h4>
            </div>
            <div class="card-body">
                <div class="btn-group-vertical w-100">
                    <a href="index.php" class="btn btn-primary text-start">Dashboard</a>
                    <a href="view_items.php" class="btn btn-primary text-start">View Items</a>
                    <a href="add_item.php" class="btn btn-success text-start">Add Item</a>
                    <a href="sell_item.php" class="btn btn-danger text-start">Record Sale</a>
                    <a href="purchase_item.php" class="btn btn-warning text-start">Record Purchase</a>
                    <a href="audit_log.php" class="btn btn-info text-start">Audit Log</a>
                    <a href="db_init.php" class="btn btn-secondary text-start">Reinitialize Database</a>
                </div>
            </div>
        </div>

        <!-- Documentation -->
        <div class="card mb-5">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0">📚 Documentation</h4>
            </div>
            <div class="card-body">
                <p>For detailed database structure information, see <a href="DATABASE_STRUCTURE.md" target="_blank"><strong>DATABASE_STRUCTURE.md</strong></a></p>
                <p>Helper functions available in <code>db_connect.php</code>:</p>
                <ul>
                    <li><code>execute_query($query)</code> - Execute SQL query safely</li>
                    <li><code>get_row($query)</code> - Get single row result</li>
                    <li><code>get_rows($query)</code> - Get all rows</li>
                    <li><code>safe_string($string)</code> - Escape string safely</li>
                    <li><code>log_action($item_id, $type, $details)</code> - Log audit action</li>
                    <li><code>currency($amount)</code> - Format currency</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
