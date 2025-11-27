<?php
include 'session_config.php';
include 'db_connect.php';

// Require login
require_login();

if (!isset($_GET['id'])) { header('Location: view_items.php'); exit; }
$id = (int) $_GET['id'];
$r = mysqli_query($conn, "SELECT item_name FROM items WHERE item_id=$id");
$row = mysqli_fetch_assoc($r);
$iname = $row ? mysqli_real_escape_string($conn, $row['item_name']) : '';
$del = mysqli_query($conn, "DELETE FROM items WHERE item_id=$id");
if ($del) {
  $details = "Deleted item: $iname";
  $log = "INSERT INTO actions (item_id, action_type, action_date, details) VALUES ($id, 'DELETE', NOW(), '".mysqli_real_escape_string($conn,$details)."')";
  mysqli_query($conn, $log);
  header('Location: view_items.php?msg=deleted');
  exit;
} else {
  echo 'Error: '.mysqli_error($conn);
}
?>