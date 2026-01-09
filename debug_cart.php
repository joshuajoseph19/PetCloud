<?php
require_once 'db_connect.php';
$stmt = $pdo->query("DESCRIBE cart");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>