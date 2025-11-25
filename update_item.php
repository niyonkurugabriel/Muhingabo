<?php
include 'db_connect.php';
if (!isset($_GET['id'])) { header('Location: view_items.php'); exit; }
$id = (int) $_GET['id'];
$res = mysqli_query($conn, "SELECT * FROM items WHERE item_id = $id");
$item = mysqli_fetch_assoc($res);
if (!$item) { header('Location: view_items.php'); exit; }
?>
<!doctype html>
<html>
<head>
<title>MUHINGABO - Edit Item</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="p-4">
<div class="container">
<div class="card shadow-sm">
  <div class="card-body">
    <h3 style="color: #fff;">Edit Item</h3>
    <form action="save_update.php" method="POST" class="row g-3" enctype="multipart/form-data">
      <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
      <div class="col-md-6"><label class="form-label">Item Name</label><input class="form-control" name="item_name" value="<?php echo htmlspecialchars($item['item_name']); ?>"></div>
      <div class="col-md-6"><label class="form-label">Category</label><input class="form-control" name="category" value="<?php echo htmlspecialchars($item['category']); ?>"></div>
      <div class="col-md-4"><label class="form-label">Quantity</label><input class="form-control" name="quantity" type="number" value="<?php echo htmlspecialchars($item['quantity']); ?>"></div>
      <div class="col-md-4"><label class="form-label">Price</label><input class="form-control" name="price" type="number" step="0.01" value="<?php echo htmlspecialchars($item['price']); ?>"></div>
      <div class="col-md-4"><label class="form-label">Supplier</label><input class="form-control" name="supplier" value="<?php echo htmlspecialchars($item['supplier']); ?>"></div>
      <div class="col-md-6">
        <label class="form-label">Image (optional)</label>
        <?php if (!empty($item['image'])): ?>
          <div class="mb-2"><img src="<?php echo htmlspecialchars($item['image']); ?>" style="max-height:100px; border-radius:0.5rem;"></div>
        <?php endif; ?>
        <input type="file" name="image" accept="image/*" class="form-control">
      </div>
      <div class="col-12"><button type="submit" class="btn btn-primary">Save Changes</button></div>
    </form>
  </div>
</div>
</div>
</body>
</html>
