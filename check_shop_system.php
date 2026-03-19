<?php
require_once 'db_connect.php';
$tables = ['products', 'orders', 'order_items'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "[$t]: " . implode(", ", $cols) . "\n\n";
    } catch (Exception $e) {
        echo "[$t] Error: " . $e->getMessage() . "\n\n";
    }
}
?>