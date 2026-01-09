<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, shop_name, status FROM shop_applications WHERE status = 'approved' LIMIT 1");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>