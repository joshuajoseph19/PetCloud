<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, shop_name, email FROM shop_applications WHERE id = 1");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>