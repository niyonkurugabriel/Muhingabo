<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: sell_item.php'); exit; }

$item_id = (int) $_POST['item_id'];
$quantity = (int) $_POST['quantity'];
$price = (float) $_POST['price'];

// basic validation
if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
	header('Location: sell_item.php?error=invalid');
	exit;
}

// check stock & item exists
$res = mysqli_query($conn, "SELECT quantity, item_name FROM items WHERE item_id = $item_id LIMIT 1");
if (!$res || mysqli_num_rows($res) == 0) { header('Location: sell_item.php?error=invalid'); exit; }
$row = mysqli_fetch_assoc($res);
if ($row['quantity'] < $quantity) { header('Location: sell_item.php?error=stock'); exit; }

$total = $quantity * $price;

// start transaction
mysqli_begin_transaction($conn);
$ok = true;
$q1 = "INSERT INTO sales (item_id, quantity, price, total, sale_date, details) VALUES ($item_id, $quantity, $price, $total, NOW(), '')";
if(!mysqli_query($conn, $q1)) $ok = false;
$q2 = "UPDATE items SET quantity = quantity - $quantity, last_modified = NOW() WHERE item_id = $item_id";
if(!mysqli_query($conn, $q2)) $ok = false;

// log action
$details = "Sale: {$quantity} x {$row['item_name']} at ".number_format($price,2);
$q3 = "INSERT INTO actions (item_id, action_type, action_date, details) VALUES ($item_id, 'SALE', NOW(), '".mysqli_real_escape_string($conn,$details)."')";
if(!mysqli_query($conn, $q3)) $ok = false;

if ($ok) { mysqli_commit($conn); header('Location: sales_dashboard.php?msg=ok'); exit; }
else { mysqli_rollback($conn); echo 'Error: '.mysqli_error($conn); }
?>