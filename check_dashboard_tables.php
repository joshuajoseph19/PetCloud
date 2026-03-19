<?php
require_once 'db_connect.php';
$tables = ['health_reminders', 'feeding_schedules', 'appointments', 'adoption_listings', 'user_pets'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        echo "[$t] exists.\n";
    } catch (Exception $e) {
        echo "[$t] DOES NOT EXIST: " . $e->getMessage() . "\n";
    }
}
?>