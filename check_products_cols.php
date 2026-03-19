<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("DESCRIBE products");
    echo "Columns in products:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>