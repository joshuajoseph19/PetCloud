<?php
require_once 'db_connect.php';

try {
    $stmt = $pdo->prepare("SELECT id, name, category_id FROM services WHERE category_id = 1");
    $stmt->execute();
    $services = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Services in Category 1:\n";
    foreach ($services as $s) {
        echo "ID: " . $s['id'] . " | Name: " . $s['name'] . "\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>