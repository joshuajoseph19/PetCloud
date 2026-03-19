<?php
require_once 'db_connect.php';

echo "--- adoption_listings ---\n";
try {
    $stmt = $pdo->query("SELECT * FROM adoption_listings");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n--- pet_rehoming_listings ---\n";
try {
    $stmt = $pdo->query("SELECT * FROM pet_rehoming_listings");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>