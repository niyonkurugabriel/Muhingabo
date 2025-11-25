<?php
include 'db_connect.php';

// Create sales table if not exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'sales'");
if (mysqli_num_rows($res) == 0) {
    $q = "CREATE TABLE sales (
        sale_id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(12,2) NOT NULL,
        sale_date DATETIME NOT NULL,
        details TEXT,
        INDEX (item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (mysqli_query($conn, $q)) echo "Created table 'sales'.\n"; else echo "Error creating sales: " . mysqli_error($conn) . "\n";
} else {
    echo "Table 'sales' already exists.\n";
}

// Create purchases table if not exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'purchases'");
if (mysqli_num_rows($res) == 0) {
    $q = "CREATE TABLE purchases (
        purchase_id INT AUTO_INCREMENT PRIMARY KEY,
        item_id INT NOT NULL,
        quantity INT NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        total DECIMAL(12,2) NOT NULL,
        purchase_date DATETIME NOT NULL,
        details TEXT,
        INDEX (item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    if (mysqli_query($conn, $q)) echo "Created table 'purchases'.\n"; else echo "Error creating purchases: " . mysqli_error($conn) . "\n";
} else {
    echo "Table 'purchases' already exists.\n";
}

// Ensure items.image column exists (if earlier migration not run)
$res = mysqli_query($conn, "SHOW COLUMNS FROM items LIKE 'image'");
if(mysqli_num_rows($res) == 0) {
    $q = "ALTER TABLE items ADD COLUMN image VARCHAR(255) DEFAULT NULL";
    if(mysqli_query($conn, $q)) {
        echo "Column 'image' added successfully.\n";
    } else {
        echo "Error adding column: ".mysqli_error($conn)."\n";
    }
} else {
    echo "Column 'image' already exists.\n";
}

?>