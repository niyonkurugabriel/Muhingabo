<?php
include 'db_connect.php';


$res = mysqli_query($conn, "SHOW COLUMNS FROM items LIKE 'image'");
if(mysqli_num_rows($res) == 0) {
    $q = "ALTER TABLE items ADD COLUMN image VARCHAR(255) DEFAULT NULL";
    if(mysqli_query($conn, $q)) {
        echo "Column 'image' added successfully.";
    } else {
        echo "Error adding column: ".mysqli_error($conn);
    }
} else {
    echo "Column 'image' already exists.";
}

?>