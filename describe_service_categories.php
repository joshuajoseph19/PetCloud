<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("DESCRIBE service_categories");
    echo "service_categories Columns:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>