<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("SELECT pet_name, status FROM adoption_listings WHERE pet_name = 'akku'");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
?>