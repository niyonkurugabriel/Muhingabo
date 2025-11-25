<?php
include 'db_connect.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : '';
$low_stock = isset($_GET['low_stock']) && $_GET['low_stock'] == 1 ? true : false;

$sql = "SELECT * FROM items WHERE 1=1";

if($search != '') { $sql .= " AND item_name LIKE '%$search%'"; }
if($category != '') { $sql .= " AND category='$category'"; }
if($low_stock) { $sql .= " AND quantity < 5"; }

$sql .= " ORDER BY item_id DESC";
$res = mysqli_query($conn, $sql);

if (!$res) {
    echo json_encode(['error' => mysqli_error($conn)]);
    exit;
}

$items = [];
while($row = mysqli_fetch_assoc($res)){
    $row['low_stock'] = $row['quantity'] < 5 ? true : false;
    $items[] = $row;
}
header('Content-Type: application/json');
echo json_encode($items);
?>