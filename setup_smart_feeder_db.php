<?php
require_once 'db_connect.php';

try {
    $sql = file_get_contents('database/smart_feeder_setup.sql');
    $pdo->exec($sql);
    echo "Smart Feeder tables setup successfully!";
} catch (PDOException $e) {
    echo "Error setting up tables: " . $e->getMessage();
}
?>