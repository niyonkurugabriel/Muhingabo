<?php
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = trim($_POST['item_name']);
    $category = trim($_POST['category']);
    $quantity = (int) $_POST['quantity'];
    $price = (float) $_POST['price'];
    $supplier = trim($_POST['supplier']);
    
    // Handle Image Upload or Selection
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['image'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/heic'];

        if (in_array($file['type'], $allowed_types)) {
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            // Create safe filename
            $filename = uniqid('item_', true) . '.' . $ext;
            $upload_dir = __DIR__ . '/uploads/';

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $upload_dir . $filename)) {
                $image_path = 'uploads/' . $filename;
            }
        }
    } elseif (isset($_POST['existing_image']) && !empty($_POST['existing_image'])) {
        // Use existing image from photos/
        $existing_file = basename($_POST['existing_image']); // Prevent directory traversal
        $image_path = 'photos/' . $existing_file;
    }

    // Insert into database
    if ($image_path === null) {
        $sql = "INSERT INTO items (item_name, category, quantity, price, supplier, image, date_added, last_modified)
                VALUES (?, ?, ?, ?, ?, NULL, NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssids", $item_name, $category, $quantity, $price, $supplier);
    } else {
        $sql = "INSERT INTO items (item_name, category, quantity, price, supplier, image, date_added, last_modified)
                VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssidss", $item_name, $category, $quantity, $price, $supplier, $image_path);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $item_id = mysqli_insert_id($conn);
        // Log action
        log_action($item_id, 'ADD', "Added new item: $item_name (Qty: $quantity)");
        
        header('Location: view_items.php?msg=added');
    } else {
        // Handle duplicate entry or error
        header('Location: add_item.php?error=' . urlencode(mysqli_error($conn)));
    }
    mysqli_stmt_close($stmt);
} else {
    header('Location: add_item.php');
}
?>