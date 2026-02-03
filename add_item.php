<?php
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();
?>
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

  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <h4 class="mb-0">➕ Add New Item</h4>
          </div>
          <div class="card-body">
            <form action="save_item.php" method="POST" enctype="multipart/form-data" class="row g-3">
              <div class="col-md-6"><label class="form-label">Item Name</label><input type="text" name="item_name" class="form-control" required></div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <input type="text" name="category" class="form-control" list="catList" required>
                <datalist id="catList"><?php $res = mysqli_query($conn, "SELECT DISTINCT category FROM items"); while($r=mysqli_fetch_assoc($res)) echo "<option value='".htmlspecialchars($r['category'])."'>"; ?></datalist>
              </div>
              <div class="col-md-6"><label class="form-label">Quantity (Initial Stock)</label><input type="number" name="quantity" class="form-control" value="0" min="0" required></div>
              <div class="col-md-6"><label class="form-label">Price</label><input type="number" name="price" class="form-control" step="0.01" required></div>
              <div class="col-md-6"><label class="form-label">Supplier</label><input type="text" name="supplier" class="form-control"></div>
              <div class="col-md-6"><label class="form-label">Upload Item Image</label><input type="file" name="image" class="form-control" accept="image/*,.heic"></div>
              <div class="col-md-6"><label class="form-label">Or Select Existing Image</label>
                <select name="existing_image" class="form-control">
                  <option value="">None</option>
                  <?php
                  $photos_dir = 'photos/';
                  if (is_dir($photos_dir)) {
                    $files = array_diff(scandir($photos_dir), array('.', '..'));
                    foreach($files as $file) {
                      if (is_file($photos_dir . $file)) {
                        echo "<option value='$file'>$file</option>";
                      }
                    }
                  }
                  ?>
                </select>
              </div>
              
              <div class="col-12 mt-4">
                <button type="submit" class="btn btn-primary w-100">Save Item</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>