<?php
require_once 'db_connect.php';
$tables = ['feeding_logs', 'feed_commands', 'feed_logs', 'smart_feeder_schedules'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("DESCRIBE $t");
        echo "Table $t:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } catch (PDOException $e) {
        echo "Table $t ERROR: " . $e->getMessage() . "\n";
    }
    echo "\n";
}
?>