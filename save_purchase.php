<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: purchase_item.php'); exit; }

$item_id = (int) $_POST['item_id'];
$quantity = (int) $_POST['quantity'];
$price = (float) $_POST['price'];

$total = $quantity * $price;

mysqli_begin_transaction($conn);
$ok = true;
$q1 = "INSERT INTO purchases (item_id, quantity, price, total, purchase_date, details) VALUES ($item_id, $quantity, $price, $total, NOW(), '')";
if(!mysqli_query($conn, $q1)) $ok = false;
$q2 = "UPDATE items SET quantity = quantity + $quantity, last_modified = NOW() WHERE item_id = $item_id";
if(!mysqli_query($conn, $q2)) $ok = false;

// log action
$res = mysqli_query($conn, "SELECT item_name FROM items WHERE item_id = $item_id LIMIT 1");
$row = mysqli_fetch_assoc($res);
$details = "Purchase: {$quantity} x {$row['item_name']} at ".number_format($price,2);
$q3 = "INSERT INTO actions (item_id, action_type, action_date, details) VALUES ($item_id, 'PURCHASE', NOW(), '".mysqli_real_escape_string($conn,$details)."')";
if(!mysqli_query($conn, $q3)) $ok = false;

if ($ok) { mysqli_commit($conn); header('Location: purchase_dashboard.php?msg=ok'); exit; }
else { mysqli_rollback($conn); echo 'Error: '.mysqli_error($conn); }
?>