<?php
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = (int) $_POST['item_id'];
  $item_name = mysqli_real_escape_string($conn, $_POST['item_name']);
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $quantity = (int) $_POST['quantity'];
  $price = (float) $_POST['price'];
  $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
  $now = date('Y-m-d H:i:s');

  // Handle optional image upload
  $image_update = "";
  if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $up = $_FILES['image'];
    if ($up['error'] === UPLOAD_ERR_OK) {
      $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
      if (in_array($up['type'], $allowed)) {
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $safe = preg_replace('/[^a-z0-9_-]/i','',pathinfo($up['name'], PATHINFO_FILENAME));
        $fname = $safe.'-'.time().'.'.$ext;
        $dest = __DIR__ . '/uploads/' . $fname;
        if (move_uploaded_file($up['tmp_name'], $dest)) {
          $image_path = 'uploads/' . $fname;
          $image_update = ", image='".mysqli_real_escape_string($conn,$image_path)."'";
        }
      }
    }
  }

  $sql = "UPDATE items SET item_name='$item_name', category='$category', quantity=$quantity, price=$price, supplier='$supplier', last_modified='$now'$image_update WHERE item_id=$id";
  if (mysqli_query($conn, $sql)) {
    $details = "Updated item: $item_name (qty: $quantity)";
    $log = "INSERT INTO actions (item_id, action_type, action_date, details) VALUES ($id, 'UPDATE', NOW(), '".mysqli_real_escape_string($conn,$details)."')";
    mysqli_query($conn, $log);
    header('Location: view_items.php?msg=updated');
    exit;
  } else {
    echo 'Error: '.mysqli_error($conn);
  }
} else {
  header('Location: view_items.php');
  exit;
}
?>