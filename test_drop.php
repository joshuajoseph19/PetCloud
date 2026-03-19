<?php
require_once 'db_connect.php';
try {
    $pdo->exec("DROP TABLE IF EXISTS health_reminders");
    echo "Dropped successfully\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
