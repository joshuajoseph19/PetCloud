<?php
require_once 'db_connect.php';
$tables = ['health_reminders', 'feeding_schedules', 'adoption_listings', 'user_pets'];
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