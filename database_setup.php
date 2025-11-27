<?php
/**
 * Database Setup Instructions - Visual Guide
 * Open this in browser for step-by-step instructions
 * 
 * Access via: http://localhost/invetory_system/database_setup.php
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Setup Instructions - Muhingabo Inventory</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
        }
        .container-custom {
            max-width: 900px;
            margin: 0 auto;
        }
        .header-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
        }
        .header-card h1 {
            color: #667eea;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .header-card p {
            color: #666;
            font-size: 16px;
        }
        .step-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin-bottom: 20px;
            border-left: 5px solid #667eea;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .step-number {
            display: inline-block;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            text-align: center;
            line-height: 50px;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .step-card h3 {
            color: #333;
            margin-top: 0;
        }
        .code-block {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            overflow-x: auto;
            margin: 15px 0;
        }
        .btn-custom {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: scale(1.05);
            color: white;
            text-decoration: none;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .feature-list li:before {
            content: "✓ ";
            color: #28a745;
            font-weight: bold;
            margin-right: 10px;
        }
        .feature-list li:last-child {
            border-bottom: none;
        }
        .status-box {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .status-box h4 {
            margin: 0;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .warning-box {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .success-box {
            background: #d4edda;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 4px;
            margin: 15px 0;
        }
        .table-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .table-item {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .table-item strong {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="container-custom">
        <!-- Header -->
        <div class="header-card">
            <h1>🎉 Muhingabo Inventory System</h1>
            <p style="font-size: 18px; margin-bottom: 0;"><strong>Database Setup Instructions</strong></p>
            <p style="color: #28a745; font-weight: bold;">Your professional database is ready!</p>
        </div>

        <!-- Status -->
        <div class="status-box">
            <h4>✅ Database System Ready</h4>
            <p style="margin: 10px 0 0 0; font-size: 14px;">9 professional tables • Full audit trail • Analytics ready</p>
        </div>

        <!-- 3 Simple Steps -->
        <div class="step-card">
            <h2 style="color: #667eea; text-align: center; margin-bottom: 30px;">⚡ Quick Start (3 Steps)</h2>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 20px;">
                <div style="text-align: center;">
                    <div class="step-number">1</div>
                    <h5>Initialize DB</h5>
                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Create all tables</p>
                    <a href="db_init.php" class="btn-custom" style="font-size: 14px; padding: 10px 20px;">Open db_init.php</a>
                </div>
                <div style="text-align: center;">
                    <div class="step-number">2</div>
                    <h5>Verify Setup</h5>
                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Check everything</p>
                    <a href="db_verify.php" class="btn-custom" style="font-size: 14px; padding: 10px 20px;">Open db_verify.php</a>
                </div>
                <div style="text-align: center;">
                    <div class="step-number">3</div>
                    <h5>Start Using</h5>
                    <p style="font-size: 14px; color: #666; margin-bottom: 10px;">Begin operations</p>
                    <a href="index.php" class="btn-custom" style="font-size: 14px; padding: 10px 20px;">Go to Dashboard</a>
                </div>
            </div>
        </div>

        <!-- Detailed Steps -->
        <div class="step-card">
            <div class="step-number">1</div>
            <h3>Step 1: Initialize Database</h3>
            <p>Open this link in your browser to create all database tables:</p>
            <div class="code-block">
                http://localhost/invetory_system/db_init.php
            </div>
            <div class="success-box">
                <strong>✓ What happens:</strong> All 9 tables are created automatically with proper structure
            </div>
            <p><strong>Expected Result:</strong> You'll see a message saying "All tables created successfully"</p>
        </div>

        <div class="step-card">
            <div class="step-number">2</div>
            <h3>Step 2: Verify Everything Works</h3>
            <p>Open this dashboard to verify the setup:</p>
            <div class="code-block">
                http://localhost/invetory_system/db_verify.php
            </div>
            <div class="info-box">
                <strong>ℹ️ This shows:</strong> All tables with status, statistics, low stock alerts, today's summary
            </div>
            <p><strong>Expected Result:</strong> All 9 tables show "✓ Exists"</p>
        </div>

        <div class="step-card">
            <div class="step-number">3</div>
            <h3>Step 3: Start Using Your System</h3>
            <p>Go to your main dashboard and start using the inventory system normally:</p>
            <div class="code-block">
                http://localhost/invetory_system/
            </div>
            <p><strong>You can now:</strong></p>
            <ul class="feature-list">
                <li>Add items with categories & suppliers</li>
                <li>Upload product images</li>
                <li>Record sales transactions</li>
                <li>Record purchase transactions</li>
                <li>View complete audit trail</li>
                <li>Generate reports & analytics</li>
            </ul>
        </div>

        <!-- Database Tables Overview -->
        <div class="step-card">
            <h3>📊 Database Tables Created</h3>
            <div class="table-grid">
                <div class="table-item">
                    <strong>items</strong><br>
                    <small>Main inventory stock</small>
                </div>
                <div class="table-item">
                    <strong>sales</strong><br>
                    <small>Sales transactions</small>
                </div>
                <div class="table-item">
                    <strong>purchases</strong><br>
                    <small>Purchase transactions</small>
                </div>
                <div class="table-item">
                    <strong>actions</strong><br>
                    <small>Audit trail</small>
                </div>
                <div class="table-item">
                    <strong>stock_history</strong><br>
                    <small>Quantity changes</small>
                </div>
                <div class="table-item">
                    <strong>categories</strong><br>
                    <small>Category master</small>
                </div>
                <div class="table-item">
                    <strong>suppliers</strong><br>
                    <small>Supplier info</small>
                </div>
                <div class="table-item">
                    <strong>price_changes</strong><br>
                    <small>Price history</small>
                </div>
                <div class="table-item">
                    <strong>daily_reports</strong><br>
                    <small>Daily summaries</small>
                </div>
            </div>
        </div>

        <!-- Features -->
        <div class="step-card">
            <h3>✨ What You Get</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
                <div>
                    <h5 style="color: #667eea;">🔐 Security</h5>
                    <ul class="feature-list" style="padding-left: 0;">
                        <li>SQL injection prevention</li>
                        <li>Input validation</li>
                        <li>Error logging</li>
                        <li>Foreign key constraints</li>
                    </ul>
                </div>
                <div>
                    <h5 style="color: #667eea;">⚡ Performance</h5>
                    <ul class="feature-list" style="padding-left: 0;">
                        <li>Strategic indexing</li>
                        <li>Optimized queries</li>
                        <li>Fast lookups</li>
                        <li>Efficient design</li>
                    </ul>
                </div>
                <div>
                    <h5 style="color: #667eea;">📊 Analytics</h5>
                    <ul class="feature-list" style="padding-left: 0;">
                        <li>Complete audit trail</li>
                        <li>Stock history</li>
                        <li>Price tracking</li>
                        <li>Daily reports</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Documentation -->
        <div class="step-card">
            <h3>📚 Documentation & Guides</h3>
            <p>Read these files for more information:</p>
            <div class="table-grid">
                <div class="table-item">
                    <strong>QUICK_REFERENCE.md</strong><br>
                    <small>Quick commands & functions</small>
                </div>
                <div class="table-item">
                    <strong>SETUP_GUIDE.md</strong><br>
                    <small>Detailed instructions</small>
                </div>
                <div class="table-item">
                    <strong>DATABASE_STRUCTURE.md</strong><br>
                    <small>Table schemas & design</small>
                </div>
                <div class="table-item">
                    <strong>config.example.php</strong><br>
                    <small>Configuration template</small>
                </div>
            </div>
        </div>

        <!-- Helper Functions -->
        <div class="step-card">
            <h3>💻 New Helper Functions</h3>
            <p>Use these in your PHP code (after including db_connect.php):</p>
            <div class="code-block">
// Get data<br>
$item = get_row("SELECT * FROM items WHERE id = 1");<br>
$items = get_rows("SELECT * FROM items");<br>
<br>
// Execute queries<br>
execute_query("UPDATE items SET qty = 10 WHERE id = 1");<br>
<br>
// Log actions<br>
log_action(5, 'UPDATE', 'Changed price');<br>
<br>
// Format currency<br>
echo currency(1500);  // FRW 1,500.00<br>
            </div>
        </div>

        <!-- Troubleshooting -->
        <div class="step-card">
            <h3>❓ Troubleshooting</h3>
            <div class="warning-box">
                <strong>⚠️ Tables don't exist?</strong><br>
                Open <a href="db_init.php">db_init.php</a> in your browser
            </div>
            <div class="warning-box">
                <strong>⚠️ Connection failed?</strong><br>
                Make sure MySQL is running in XAMPP Control Panel
            </div>
            <div class="warning-box">
                <strong>⚠️ Need diagnostics?</strong><br>
                Open <a href="db_verify.php">db_verify.php</a> to see current status
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="step-card" style="text-align: center;">
            <h3>🔗 Quick Navigation</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-top: 20px;">
                <a href="db_init.php" class="btn-custom">Initialize DB</a>
                <a href="db_verify.php" class="btn-custom">Verify Setup</a>
                <a href="index.php" class="btn-custom">Dashboard</a>
                <a href="view_items.php" class="btn-custom">View Items</a>
                <a href="add_item.php" class="btn-custom">Add Item</a>
                <a href="sell_item.php" class="btn-custom">Record Sale</a>
                <a href="purchase_item.php" class="btn-custom">Record Purchase</a>
                <a href="audit_log.php" class="btn-custom">Audit Log</a>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; color: white; padding: 30px 0; font-size: 14px;">
            <p>Setup Date: November 27, 2025 | System: Muhingabo Hardware Inventory | Status: ✅ READY</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
