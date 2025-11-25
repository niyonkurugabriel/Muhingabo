<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: sell_item.php'); exit; }

$item_id = (int) $_POST['item_id'];
$quantity = (int) $_POST['quantity'];
$price = floatval($_POST['price']);

// basic validation
if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
	header('Location: sell_item.php?error=invalid');
	exit;
}

// check stock & item exists (use procedural mysqli)
$check_stmt = mysqli_prepare($conn, "SELECT quantity, item_name FROM items WHERE item_id = ? LIMIT 1");
mysqli_stmt_bind_param($check_stmt, "i", $item_id);
mysqli_stmt_execute($check_stmt);
$check_res = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($check_res) == 0) { 
	header('Location: sell_item.php?error=invalid'); 
	exit; 
}
$row = mysqli_fetch_assoc($check_res);
if ($row['quantity'] < $quantity) { 
	header('Location: sell_item.php?error=stock'); 
	exit; 
}

$total = $quantity * $price;
$item_name = $row['item_name'];

// start transaction
mysqli_begin_transaction($conn);
$ok = true;

// Insert sale
$stmt1 = mysqli_prepare($conn, "INSERT INTO sales (item_id, quantity, price, total, sale_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
mysqli_stmt_bind_param($stmt1, "iidd", $item_id, $quantity, $price, $total);
if(!mysqli_stmt_execute($stmt1)) {
	$ok = false;
}

// Update stock
$stmt2 = mysqli_prepare($conn, "UPDATE items SET quantity = quantity - ?, last_modified = NOW() WHERE item_id = ?");
mysqli_stmt_bind_param($stmt2, "ii", $quantity, $item_id);
if(!mysqli_stmt_execute($stmt2)) {
	$ok = false;
}

// Log action
$details = "Sale: {$quantity} x {$item_name} at ".number_format($price, 2);
$stmt3 = mysqli_prepare($conn, "INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'SALE', NOW(), ?)");
mysqli_stmt_bind_param($stmt3, "is", $item_id, $details);
if(!mysqli_stmt_execute($stmt3)) {
	$ok = false;
}

if ($ok) { 
	mysqli_commit($conn); 
	header('Location: sales_dashboard.php?msg=ok'); 
	exit; 
} else { 
	mysqli_rollback($conn); 
	echo 'Error: '.mysqli_error($conn); 
}
?>