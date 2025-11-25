<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: sell_item.php'); exit; }

$item_id = isset($_POST['item_id']) ? (int) $_POST['item_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 0;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

// basic validation
if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
	header('Location: sell_item.php?error=invalid');
	exit;
}

function tx_log($msg) {
	$dir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
	if (!is_dir($dir)) @mkdir($dir, 0755, true);
	$file = $dir . DIRECTORY_SEPARATOR . 'tx_debug.log';
	$entry = date('[Y-m-d H:i:s] ') . $msg . "\n";
	@file_put_contents($file, $entry, FILE_APPEND);
}

// check stock and get item name
$check_stmt = mysqli_prepare($conn, "SELECT quantity, item_name FROM items WHERE item_id = ? LIMIT 1");
if (!$check_stmt) { tx_log('prepare failed (check_stmt sale): ' . mysqli_error($conn)); header('Location: sell_item.php?error=invalid'); exit; }
mysqli_stmt_bind_param($check_stmt, "i", $item_id);
if (!mysqli_stmt_execute($check_stmt)) { tx_log('execute failed (check_stmt sale): ' . mysqli_error($conn)); header('Location: sell_item.php?error=invalid'); exit; }
mysqli_stmt_bind_result($check_stmt, $db_quantity, $db_item_name);
if (!mysqli_stmt_fetch($check_stmt)) { tx_log('no item found for id=' . $item_id); header('Location: sell_item.php?error=invalid'); exit; }
mysqli_stmt_close($check_stmt);

if ($quantity > (int)$db_quantity) {
	header('Location: sell_item.php?error=nostock');
	exit;
}

$item_name = $db_item_name;
$total = $quantity * $price;

mysqli_begin_transaction($conn);
$ok = true;

// insert sale
$stmt1 = mysqli_prepare($conn, "INSERT INTO sales (item_id, quantity, price, total, sale_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
if (!$stmt1) { tx_log('prepare failed (insert sale): ' . mysqli_error($conn)); $ok = false; }
else {
	mysqli_stmt_bind_param($stmt1, "iidd", $item_id, $quantity, $price, $total);
	if(!mysqli_stmt_execute($stmt1)) { tx_log('execute failed (insert sale): ' . mysqli_error($conn)); $ok = false; }
	mysqli_stmt_close($stmt1);
}

// update stock
$stmt2 = mysqli_prepare($conn, "UPDATE items SET quantity = quantity - ?, last_modified = NOW() WHERE item_id = ?");
if (!$stmt2) { tx_log('prepare failed (update items sale): ' . mysqli_error($conn)); $ok = false; }
else {
	mysqli_stmt_bind_param($stmt2, "ii", $quantity, $item_id);
	if(!mysqli_stmt_execute($stmt2)) { tx_log('execute failed (update items sale): ' . mysqli_error($conn)); $ok = false; }
	mysqli_stmt_close($stmt2);
}

// log action
$details = "Sale: {$quantity} x {$item_name} at ".number_format($price, 2);
$stmt3 = mysqli_prepare($conn, "INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'SALE', NOW(), ?)");
if (!$stmt3) { tx_log('prepare failed (insert action sale): ' . mysqli_error($conn)); $ok = false; }
else {
	mysqli_stmt_bind_param($stmt3, "is", $item_id, $details);
	if(!mysqli_stmt_execute($stmt3)) { tx_log('execute failed (insert action sale): ' . mysqli_error($conn)); $ok = false; }
	mysqli_stmt_close($stmt3);
}

if ($ok) { mysqli_commit($conn); header('Location: sales_dashboard.php?msg=ok'); exit; } else { mysqli_rollback($conn); tx_log('transaction failed sale: '.mysqli_error($conn)); echo 'Error: '.mysqli_error($conn); }
?>