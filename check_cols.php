<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("DESCRIBE daily_tasks");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in daily_tasks: " . implode(", ", $columns) . "\n";

    $stmt2 = $pdo->query("DESCRIBE health_records");
    $columns2 = $stmt2->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in health_records: " . implode(", ", $columns2) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>