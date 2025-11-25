<?php include 'db_connect.php'; ?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MUHINGABO - Add Item</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container py-4">
  <div class="card shadow-sm">
    <div class="card-body">
      <h3 class="card-title mb-3" style="color: #fff;">Add New Item</h3>
      <?php if (isset($_GET['error']) && $_GET['error'] === 'duplicate'): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Duplicate Item!</strong> An item with that name already exists.
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
      <form action="save_item.php" method="POST" class="row g-3" enctype="multipart/form-data">
        <div class="col-md-6">
          <label class="form-label">Item Name</label>
          <input type="text" name="item_name" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Category</label>
          <input type="text" name="category" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Quantity</label>
          <input type="number" name="quantity" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Price</label>
          <input type="number" step="0.01" name="price" class="form-control" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Supplier</label>
          <input type="text" name="supplier" class="form-control">
        </div>
        <div class="col-12">
        </div>
        <div class="col-md-6">
          <label class="form-label">Image (optional)</label>
          <input type="file" name="image" accept="image/*" class="form-control">
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-success">Save Item</button>
        </div>
      </form>
    </div>
  </div>
</div>
</body>
</html>
