<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("SELECT * FROM services LIMIT 5");
    echo "Count: " . $stmt->rowCount() . "\n";
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>