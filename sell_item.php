<?php include 'db_connect.php'; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MUHINGABO - Sell Item</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3>Sell Item</h3>
      <?php if (isset($_GET['error']) && $_GET['error'] === 'stock'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Insufficient stock!</strong> Not enough quantity available.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif (isset($_GET['error']) && $_GET['error'] === 'invalid'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <strong>Invalid item!</strong> Please select a valid item.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <form action="save_sale.php" method="POST" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Item</label>
          <select name="item_id" class="form-select" required>
            <option value="">-- choose item --</option>
            <?php $res = mysqli_query($conn, "SELECT item_id, item_name, quantity FROM items ORDER BY item_name"); while($r = mysqli_fetch_assoc($res)) { ?>
              <option value="<?php echo $r['item_id']; ?>"><?php echo htmlspecialchars($r['item_name']).' ('.$r['quantity'].' in stock)'; ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" min="1" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Price (per unit)</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-danger">Record Sale</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>