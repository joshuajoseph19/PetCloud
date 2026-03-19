<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("DESCRIBE shop_applications");
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in shop_applications:\n";
    foreach ($cols as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>