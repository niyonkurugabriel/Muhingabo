<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $item_name = mysqli_real_escape_string($conn, trim($_POST['item_name']));
  $category = mysqli_real_escape_string($conn, $_POST['category']);
  $quantity = (int) $_POST['quantity'];
  $price = (float) $_POST['price'];
  $supplier = mysqli_real_escape_string($conn, $_POST['supplier']);
  $date_added = date('Y-m-d');
  $now = date('Y-m-d H:i:s');

  // Deny duplicate item name (case-insensitive)
  $check = mysqli_query($conn, "SELECT item_id FROM items WHERE LOWER(item_name) = LOWER('".mysqli_real_escape_string($conn,$item_name)."') LIMIT 1");
  if (!$check) {
    die('DB error: '.mysqli_error($conn));
  }
  if (mysqli_num_rows($check) > 0) {
    // redirect back with error
    header('Location: add_item.php?error=duplicate');
    exit;
  }

  // Handle optional image upload
  $image_path = null;
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
        }
      }
    }
  }

  $sql = "INSERT INTO items (item_name, category, quantity, price, supplier, date_added, last_modified, image)"
       . " VALUES ('$item_name', '$category', $quantity, $price, '$supplier', '$date_added', '$now', ".($image_path ? "'".mysqli_real_escape_string($conn,$image_path)."'" : "NULL").")";

  if (mysqli_query($conn, $sql)) {
    $item_id = mysqli_insert_id($conn);
    $details = "Added item: $item_name (qty: $quantity)";
    $log = "INSERT INTO actions (item_id, action_type, action_date, details) VALUES ($item_id, 'ADD', NOW(), '".mysqli_real_escape_string($conn,$details)."')";
    mysqli_query($conn, $log);
    header('Location: view_items.php?msg=added');
    exit;
  } else {
    echo 'Error: ' . mysqli_error($conn);
  }
} else {
  header('Location: add_item.php');
  exit;
}
?>