<?php include 'db_connect.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Purchase Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body class="bg-light">
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="mb-3">
    <a href="purchase_item.php" class="btn btn-success">📦 Record New Purchase</a>
  </div>
  <div class="card shadow-sm">
    <div class="card-body">
      <h3>Purchase History</h3>
      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'ok'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Purchase recorded!</strong> Stock has been updated.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <table class="table table-sm table-hover">
        <thead>
          <tr><th>#</th><th>Item</th><th>Qty</th><th>Price</th><th>Total</th><th>Date</th></tr>
        </thead>
        <tbody>
          <?php
            $res = mysqli_query($conn, "SELECT p.*, i.item_name FROM purchases p LEFT JOIN items i ON p.item_id = i.item_id ORDER BY p.purchase_date DESC LIMIT 200");
            if (!$res) {
              echo '<tr><td colspan="6" class="text-center text-danger">Database table not found. Please run migration: <a href="migrate_create_sales_purchases.php">Click here</a></td></tr>';
            } else {
              if (mysqli_num_rows($res) == 0) {
                echo '<tr><td colspan="6" class="text-center text-muted">No purchases recorded yet.</td></tr>';
              }
              while($r = mysqli_fetch_assoc($res)) {
                echo '<tr>';
                echo '<td>'.htmlspecialchars($r['purchase_id']).'</td>';
                echo '<td>'.htmlspecialchars($r['item_name'] ?? 'N/A').'</td>';
                echo '<td>'.htmlspecialchars($r['quantity']).'</td>';
                echo '<td>'.number_format($r['price'],2).'</td>';
                echo '<td>'.number_format($r['total'],2).'</td>';
                echo '<td>'.htmlspecialchars($r['purchase_date']).'</td>';
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