<?php
require_once 'db_connect.php';
$checkTables = ['pet_types', 'breeds', 'adoption_pet_types', 'adoption_breeds'];
foreach ($checkTables as $t) {
    try {
        $count = $pdo->query("SELECT COUNT(*) FROM $t")->fetchColumn();
        echo "[$t]: $count rows\n";
    } catch (Exception $e) {
        echo "[$t] Error: " . $e->getMessage() . "\n";
    }
}
?>