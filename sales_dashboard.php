<?php 
include 'session_config.php';
include 'db_connect.php';

// Require login
require_login();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Sales Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="mb-3">
    <a href="sell_item.php" class="btn btn-danger">💰 Record New Sale</a>
  </div>
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 style="color: #fff;">Sales History</h3>
      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ok'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Sale recorded!</strong> Stock has been decremented.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <table class="table table-sm table-hover">
        <thead>
          <tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php
            $res = mysqli_query($conn, "SELECT s.*, i.item_name FROM sales s LEFT JOIN items i ON s.item_id = i.item_id ORDER BY s.sale_date DESC LIMIT 200");
            if (!$res) {
              echo '<tr><td colspan="6" class="text-center text-danger">Database table not found. Please run migration: <a href="migrate_create_sales_purchases.php">Click here</a></td></tr>';
            } else {
              if (mysqli_num_rows($res) == 0) {
                echo '<tr><td colspan="6" class="text-center text-muted">No sales recorded yet.</td></tr>';
              }
              while($r = mysqli_fetch_assoc($res)) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($r['sale_id']).'</td>';
                echo '<td>'.htmlspecialchars($r['item_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($r['quantity']).'</td>';
                echo '<td>'.currency($r['price']).'</td>';
                echo '<td>'.currency($r['total']).'</td>';
                echo '<td>'.htmlspecialchars($r['sale_date']).'</td>';
                echo '</tr>';
              }
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>