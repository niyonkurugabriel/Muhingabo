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
$stmt = $conn->prepare("SELECT quantity, item_name FROM items WHERE item_id = ? LIMIT 1");
$stmt->bind_param("i", $item_id);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows == 0) { header('Location: sell_item.php?error=invalid'); exit; }
$row = $res->fetch_assoc();
if ($row['quantity'] < $quantity) { header('Location: sell_item.php?error=stock'); exit; }

$total = $quantity * $price;

// start transaction
mysqli_begin_transaction($conn);
$ok = true;

// Insert sale
$stmt1 = $conn->prepare("INSERT INTO sales (item_id, quantity, price, total, sale_date, details) VALUES (?, ?, ?, ?, NOW(), '')");
$stmt1->bind_param("iddd", $item_id, $quantity, $price, $total);
if(!$stmt1->execute()) $ok = false;

// Update stock
$stmt2 = $conn->prepare("UPDATE items SET quantity = quantity - ?, last_modified = NOW() WHERE item_id = ?");
$stmt2->bind_param("di", $quantity, $item_id);
if(!$stmt2->execute()) $ok = false;

// Log action
$details = "Sale: {$quantity} x {$row['item_name']} at ".number_format($price,2);
$stmt3 = $conn->prepare("INSERT INTO actions (item_id, action_type, action_date, details) VALUES (?, 'SALE', NOW(), ?)");
$stmt3->bind_param("is", $item_id, $details);
if(!$stmt3->execute()) $ok = false;

if ($ok) { mysqli_commit($conn); header('Location: sales_dashboard.php?msg=ok'); exit; }
else { mysqli_rollback($conn); echo 'Error: '.mysqli_error($conn); }
?>