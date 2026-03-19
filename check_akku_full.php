<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("SELECT * FROM adoption_listings WHERE pet_name = 'akku'");
$stmt->execute();
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>