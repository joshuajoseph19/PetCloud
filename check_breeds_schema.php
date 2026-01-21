<?php
require_once 'db_connect.php';

echo "--- adoption_pet_types ---\n";
$stmt = $pdo->query("DESCRIBE adoption_pet_types");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- adoption_breeds ---\n";
$stmt = $pdo->query("DESCRIBE adoption_breeds");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- content of adoption_pet_types ---\n";
$stmt = $pdo->query("SELECT * FROM adoption_pet_types");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>