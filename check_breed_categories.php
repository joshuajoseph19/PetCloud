<?php
require_once 'db_connect.php';

echo "--- breed_categories ---\n";
$stmt = $pdo->query("DESCRIBE breed_categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- content of breed_categories ---\n";
$stmt = $pdo->query("SELECT * FROM breed_categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>