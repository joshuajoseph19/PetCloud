<?php
require_once 'db_connect.php';
$count = $pdo->exec("UPDATE products SET shop_id = 1 WHERE shop_id IS NULL");
echo "Updated $count products.";
?>