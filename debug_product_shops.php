<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, name, shop_id FROM products");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>