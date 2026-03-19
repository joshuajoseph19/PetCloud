<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("UPDATE adoption_listings SET pet_type = 'dog' WHERE pet_name = 'jokku'");
$stmt->execute();
echo "Updated jokku pet_type to 'dog'.\n";
?>