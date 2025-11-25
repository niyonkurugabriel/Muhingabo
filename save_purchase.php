<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: purchase_item.php'); exit; }

$item_id = (int) $_POST['item_id'];
$quantity = (int) $_POST['quantity'];
$price = floatval($_POST['price']);

// basic validation
if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
	header('Location: purchase_item.php?error=invalid');
	exit;
}

// ensure item exists and get name (use procedural mysqli)
$check_stmt = mysqli_prepare($conn, "SELECT item_name FROM items WHERE item_id = ? LIMIT 1");
mysqli_stmt_bind_param($check_stmt, "i", $item_id);
mysqli_stmt_execute($check_stmt);
$check_res = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_res) == 0) { 
	header('Location: purchase_item.php?error=invalid'); 
	exit; 
}
$row = mysqli_fetch_assoc($check_res);
$item_name = $row['item_name'];

$total = $quantity * $price;

mysqli_begin_transaction($conn);
$ok = true;

// Insert purchase
$stmt1 = mysqli_prepare($conn, "INSERT INTO purchases (item_id, quantity, price, total, purchase_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
mysqli_stmt_bind_param($stmt1, "iddd", $item_id, $quantity, $price, $total);
if(!mysqli_stmt_execute($stmt1)) {
	$ok = false;
}

// Update stock
$stmt2 = mysqli_prepare($conn, "UPDATE items SET quantity = quantity + ?, last_modified = NOW() WHERE item_id = ?");
mysqli_stmt_bind_param($stmt2, "ii", $quantity, $item_id);
if(!mysqli_stmt_execute($stmt2)) {
	$ok = false;
}

// log action
$details = "Purchase: {$quantity} x {$item_name} at ".number_format($price, 2);
$stmt3 = mysqli_prepare($conn, "INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'PURCHASE', NOW(), ?)");
mysqli_stmt_bind_param($stmt3, "is", $item_id, $details);
if(!mysqli_stmt_execute($stmt3)) {
	$ok = false;
}

if ($ok) { 
	mysqli_commit($conn); 
	header('Location: purchase_dashboard.php?msg=ok'); 
	exit; 
} else { 
	mysqli_rollback($conn); 
	echo 'Error: '.mysqli_error($conn); 
}
?>