<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: purchase_item.php'); exit; }

// Support multiple items
$raw_item_ids = isset($_POST['item_id']) ? $_POST['item_id'] : [];
$raw_quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
$raw_prices = isset($_POST['price']) ? $_POST['price'] : [];

$item_ids = is_array($raw_item_ids) ? $raw_item_ids : ($raw_item_ids ? [$raw_item_ids] : []);
$quantities = is_array($raw_quantities) ? $raw_quantities : ($raw_quantities ? [$raw_quantities] : []);
$prices = is_array($raw_prices) ? $raw_prices : ($raw_prices ? [$raw_prices] : []);

if (count($item_ids) === 0) { header('Location: purchase_item.php?error=invalid'); exit; }

// helper logger
function tx_log($msg) {
	$dir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
	if (!is_dir($dir)) @mkdir($dir, 0755, true);
	$file = $dir . DIRECTORY_SEPARATOR . 'tx_debug.log';
	$entry = date('[Y-m-d H:i:s] ') . $msg . "\n";
	@file_put_contents($file, $entry, FILE_APPEND);
}

// ensure item exists and get name (use prepared stmt + bind_result)
$check_stmt = mysqli_prepare($conn, "SELECT item_name FROM items WHERE item_id = ? LIMIT 1");
if (!$check_stmt) { tx_log('prepare failed (check_stmt purchase): ' . mysqli_error($conn)); header('Location: purchase_item.php?error=invalid'); exit; }
mysqli_stmt_bind_param($check_stmt, "i", $item_id);
if (!mysqli_stmt_execute($check_stmt)) { tx_log('execute failed (check_stmt purchase): ' . mysqli_error($conn)); header('Location: purchase_item.php?error=invalid'); exit; }
mysqli_stmt_bind_result($check_stmt, $db_item_name);
if (!mysqli_stmt_fetch($check_stmt)) { tx_log('no item found for id=' . $item_id); header('Location: purchase_item.php?error=invalid'); exit; }
mysqli_stmt_close($check_stmt);
$item_name = $db_item_name;

// begin transaction and process multiple purchases
mysqli_begin_transaction($conn);
$ok = true;
$errors = [];
for ($i = 0; $i < count($item_ids); $i++) {
	$item_id = (int)$item_ids[$i];
	$quantity = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
	$price = isset($prices[$i]) ? floatval($prices[$i]) : 0.0;
	if ($item_id <= 0 || $quantity <= 0 || $price <= 0) { $errors[] = 'invalid'; $ok = false; break; }

	// ensure item exists
	$check_stmt = mysqli_prepare($conn, "SELECT item_name FROM items WHERE item_id = ? LIMIT 1");
	if (!$check_stmt) { tx_log('prepare failed (check_stmt purchase): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($check_stmt, "i", $item_id);
	if (!mysqli_stmt_execute($check_stmt)) { tx_log('execute failed (check_stmt purchase): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($check_stmt); break; }
	mysqli_stmt_bind_result($check_stmt, $db_item_name);
	if (!mysqli_stmt_fetch($check_stmt)) { tx_log('no item found for id=' . $item_id); $ok = false; mysqli_stmt_close($check_stmt); break; }
	mysqli_stmt_close($check_stmt);

	$total = $quantity * $price;

	// Insert purchase row
	$stmt1 = mysqli_prepare($conn, "INSERT INTO purchases (item_id, quantity, price, total, purchase_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
	if (!$stmt1) { tx_log('prepare failed (insert purchase): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt1, "iidd", $item_id, $quantity, $price, $total);
	if(!mysqli_stmt_execute($stmt1)) { tx_log('execute failed (insert purchase): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt1); break; }
	mysqli_stmt_close($stmt1);

	// Update stock (add)
	$stmt2 = mysqli_prepare($conn, "UPDATE items SET quantity = quantity + ?, last_modified = NOW() WHERE item_id = ?");
	if (!$stmt2) { tx_log('prepare failed (update items purchase): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt2, "ii", $quantity, $item_id);
	if(!mysqli_stmt_execute($stmt2)) { tx_log('execute failed (update items purchase): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt2); break; }
	mysqli_stmt_close($stmt2);

	// log action per item
	$details = "Purchase: {$quantity} x {$db_item_name} at ".currency($price);
	$stmt3 = mysqli_prepare($conn, "INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'PURCHASE', NOW(), ?)");
	if (!$stmt3) { tx_log('prepare failed (insert action purchase): ' . mysqli_error($conn)); $ok = false; break; }
	mysqli_stmt_bind_param($stmt3, "is", $item_id, $details);
	if(!mysqli_stmt_execute($stmt3)) { tx_log('execute failed (insert action purchase): ' . mysqli_error($conn)); $ok = false; mysqli_stmt_close($stmt3); break; }
	mysqli_stmt_close($stmt3);
}

if ($ok) { mysqli_commit($conn); header('Location: purchase_dashboard.php?msg=ok'); exit; } else { mysqli_rollback($conn); header('Location: purchase_item.php?error=invalid'); exit; }
?>