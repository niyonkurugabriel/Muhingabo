<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "inventory_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Currency helper: format amounts with FRW prefix
if (!function_exists('currency')) {
    function currency($amount, $decimals = 2) {
        // Ensure numeric
        $n = is_numeric($amount) ? $amount : 0;
        return 'FRW ' . number_format($n, $decimals);
    }
}


?>
