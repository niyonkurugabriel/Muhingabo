<?php
include 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: purchase_item.php'); exit; }

$item_id = (int) $_POST['item_id'];
$quantity = (int) $_POST['quantity'];
$price = (float) $_POST['price'];

// basic validation
if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
	header('Location: purchase_item.php?error=invalid');
	exit;
}

// ensure item exists and get name
$stmt = $conn->prepare("SELECT item_name FROM items WHERE item_id = ? LIMIT 1");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) { header('Location: purchase_item.php?error=invalid'); exit; }
$row = $res->fetch_assoc();

$total = $quantity * $price;

mysqli_begin_transaction($conn);
$ok = true;

// Insert purchase
$stmt1 = $conn->prepare("INSERT INTO purchases (item_id, quantity, price, total, purchase_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
$stmt1->bind_param("iddd", $item_id, $quantity, $price, $total);
if(!$stmt1->execute()) $ok = false;

// Update stock
$stmt2 = $conn->prepare("UPDATE items SET quantity = quantity + ?, last_modified = NOW() WHERE item_id = ?");
$stmt2->bind_param("di", $quantity, $item_id);
if(!$stmt2->execute()) $ok = false;

// log action
$details = "Purchase: {$quantity} x {$row['item_name']} at ".number_format($price,2);
$stmt3 = $conn->prepare("INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'PURCHASE', NOW(), ?)");
$stmt3->bind_param("is", $item_id, $details);
if(!$stmt3->execute()) $ok = false;

if ($ok) { mysqli_commit($conn); header('Location: purchase_dashboard.php?msg=ok'); exit; }
else { mysqli_rollback($conn); echo 'Error: '.mysqli_error($conn); }
?>