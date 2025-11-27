<?php
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: sell_item.php'); exit; }

// Support multiple items: item_id[], quantity[], price[]
$raw_item_ids = isset($_POST['item_id']) ? $_POST['item_id'] : [];
$raw_quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
$raw_prices = isset($_POST['price']) ? $_POST['price'] : [];

// normalize to arrays
$item_ids = is_array($raw_item_ids) ? $raw_item_ids : ($raw_item_ids ? [$raw_item_ids] : []);
$quantities = is_array($raw_quantities) ? $raw_quantities : ($raw_quantities ? [$raw_quantities] : []);
$prices = is_array($raw_prices) ? $raw_prices : ($raw_prices ? [$raw_prices] : []);

if (count($item_ids) === 0) {
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

// begin transaction and process each item
mysqli_begin_transaction($conn);
$ok = true;
$errors = [];

for ($i = 0; $i < count($item_ids); $i++) {
	$item_id = (int)$item_ids[$i];
	$quantity = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
	$price = isset($prices[$i]) ? floatval($prices[$i]) : 0.0;
	if ($item_id <= 0 || $quantity <= 0 || $price <= 0) { $errors[] = 'invalid'; $ok = false; break; }

	// check stock and get item name
	$check_stmt = mysqli_prepare($conn, "SELECT quantity, item_name FROM items WHERE item_id = ? LIMIT 1");
	if (!$check_stmt) { tx_log('prepare failed (check_stmt sale): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($check_stmt, "i", $item_id);
	if (!mysqli_stmt_execute($check_stmt)) { tx_log('execute failed (check_stmt sale): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($check_stmt); break; }
	mysqli_stmt_bind_result($check_stmt, $db_quantity, $db_item_name);
	if (!mysqli_stmt_fetch($check_stmt)) { tx_log('no item found for id=' . $item_id); $ok = false; mysqli_stmt_close($check_stmt); break; }
	mysqli_stmt_close($check_stmt);

	if ($quantity > (int)$db_quantity) { $errors[] = 'nostock'; $ok = false; break; }

	$total = $quantity * $price;

	// insert sale row
	$stmt1 = mysqli_prepare($conn, "INSERT INTO sales (item_id, quantity, price, total, sale_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
	if (!$stmt1) { tx_log('prepare failed (insert sale): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt1, "iidd", $item_id, $quantity, $price, $total);
	if(!mysqli_stmt_execute($stmt1)) { tx_log('execute failed (insert sale): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt1); break; }
	mysqli_stmt_close($stmt1);

	// update stock
	$stmt2 = mysqli_prepare($conn, "UPDATE items SET quantity = quantity - ?, last_modified = NOW() WHERE item_id = ?");
	if (!$stmt2) { tx_log('prepare failed (update items sale): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt2, "ii", $quantity, $item_id);
	if(!mysqli_stmt_execute($stmt2)) { tx_log('execute failed (update items sale): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt2); break; }
	mysqli_stmt_close($stmt2);

	// log action per item
	$details = "Sale: {$quantity} x {$db_item_name} at ".currency($price);
	$stmt3 = mysqli_prepare($conn, "INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'SALE', NOW(), ?)");
	if (!$stmt3) { tx_log('prepare failed (insert action sale): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt3, "is", $item_id, $details);
	if(!mysqli_stmt_execute($stmt3)) { tx_log('execute failed (insert action sale): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt3); break; }
	mysqli_stmt_close($stmt3);
}

if ($ok) { mysqli_commit($conn); header('Location: sales_dashboard.php?msg=ok'); exit; } else { mysqli_rollback($conn); if (in_array('nostock', $errors)) header('Location: sell_item.php?error=nostock'); else header('Location: sell_item.php?error=invalid'); exit; }
?>