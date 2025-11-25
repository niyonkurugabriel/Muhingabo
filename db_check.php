<?php
include 'db_connect.php';
header('Content-Type: text/plain; charset=utf-8');

echo "== Latest Sales ==\n";
$res = mysqli_query($conn, "SELECT * FROM sales ORDER BY sale_date DESC LIMIT 10");
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r) . "\n";
  }
} else { echo "Error reading sales: " . mysqli_error($conn) . "\n"; }

echo "\n== Latest Purchases ==\n";
$res = mysqli_query($conn, "SELECT * FROM purchases ORDER BY purchase_date DESC LIMIT 10");
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r) . "\n";
  }
} else { echo "Error reading purchases: " . mysqli_error($conn) . "\n"; }

echo "\n== Latest Actions ==\n";
$res = mysqli_query($conn, "SELECT * FROM actions ORDER BY action_date DESC LIMIT 20");
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r) . "\n";
  }
} else { echo "Error reading actions: " . mysqli_error($conn) . "\n"; }

echo "\n== Recent Items (top 20) ==\n";
$res = mysqli_query($conn, "SELECT item_id, item_name, quantity, last_modified FROM items ORDER BY item_id DESC LIMIT 20");
if ($res) {
  while ($r = mysqli_fetch_assoc($res)) {
    echo json_encode($r) . "\n";
  }
} else { echo "Error reading items: " . mysqli_error($conn) . "\n"; }

echo "\n\nIf you just submitted a sale or purchase, check timestamps to confirm insertion.\n";
?>