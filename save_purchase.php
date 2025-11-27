<?php
require_once 'session_config.php';
include 'db_connect.php';

// Require login
require_login();

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    header('Location: purchase_item.php'); 
    exit; 
}

// Read arrays safely
$item_ids   = isset($_POST['item_id']) ? (array)$_POST['item_id'] : [];
$quantities = isset($_POST['quantity']) ? (array)$_POST['quantity'] : [];
$prices     = isset($_POST['price']) ? (array)$_POST['price'] : [];

if (count($item_ids) === 0) { 
    header('Location: purchase_item.php?error=invalid'); 
    exit; 
}

// Debug logger
function tx_log($msg) {
    $dir = __DIR__ . '/logs';
    if (!is_dir($dir)) @mkdir($dir, 0755, true);
    @file_put_contents($dir . '/tx_debug.log', date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
}

mysqli_begin_transaction($conn);
$ok = true;

for ($i = 0; $i < count($item_ids); $i++) {

    $item_id  = (int)$item_ids[$i];
    $quantity = isset($quantities[$i]) ? (int)$quantities[$i] : 0;
    $price    = isset($prices[$i]) ? floatval($prices[$i]) : 0;

    if ($item_id <= 0 || $quantity <= 0 || $price <= 0) {
        tx_log("Invalid data: id=$item_id qty=$quantity price=$price");
        $ok = false; 
        break;
    }

    // Check item exists
    $check = mysqli_prepare($conn, "SELECT item_name FROM items WHERE item_id = ? LIMIT 1");
    if (!$check) { tx_log(mysqli_error($conn)); $ok = false; break; }

    mysqli_stmt_bind_param($check, "i", $item_id);
    mysqli_stmt_execute($check);
    mysqli_stmt_bind_result($check, $item_name);

    if (!mysqli_stmt_fetch($check)) {
        tx_log("Item not found: id=$item_id");
        mysqli_stmt_close($check);
        $ok = false;
        break;
    }
    mysqli_stmt_close($check);

    $total = $quantity * $price;

    // Insert purchase
    $ins = mysqli_prepare($conn, 
        "INSERT INTO purchases (item_id, quantity, price, total, purchase_date, details)
         VALUES (?, ?, ?, ?, NOW(), '')"
    );
    if (!$ins) { tx_log(mysqli_error($conn)); $ok = false; break; }

    mysqli_stmt_bind_param($ins, "iidd", $item_id, $quantity, $price, $total);
    if (!mysqli_stmt_execute($ins)) { tx_log(mysqli_error($conn)); $ok = false; }
    mysqli_stmt_close($ins);

    // Update stock
    $up = mysqli_prepare($conn, 
        "UPDATE items SET quantity = quantity + ?, last_modified = NOW() WHERE item_id = ?"
    );
    if (!$up) { tx_log(mysqli_error($conn)); $ok = false; break; }

    mysqli_stmt_bind_param($up, "ii", $quantity, $item_id);
    if (!mysqli_stmt_execute($up)) { tx_log(mysqli_error($conn)); $ok = false; }
    mysqli_stmt_close($up);

    // Log action
    $details = "Purchase: {$quantity} x {$item_name} at FRW " . number_format($price,2);
    $log = mysqli_prepare($conn, 
        "INSERT INTO actions (item_id, action_type, action_date, details)
         VALUES (?, 'PURCHASE', NOW(), ?)"
    );
    if (!$log) { tx_log(mysqli_error($conn)); $ok = false; break; }

    mysqli_stmt_bind_param($log, "is", $item_id, $details);
    if (!mysqli_stmt_execute($log)) { tx_log(mysqli_error($conn)); $ok = false; }
    mysqli_stmt_close($log);
}

if ($ok) {
    mysqli_commit($conn);
    header("Location: purchase_dashboard.php?msg=ok");
    exit;
} else {
    mysqli_rollback($conn);
    header("Location: purchase_item.php?error=invalid");
    exit;
}
?>
