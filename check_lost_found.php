<?php
require_once 'db_connect.php';
$tables = ['lost_pet_alerts', 'found_pet_reports'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "[$t]: " . implode(", ", $cols) . "\n\n";
    } catch (Exception $e) {
        echo "[$t] DOES NOT EXIST.\n\n";
    }
}
?>