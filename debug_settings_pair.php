<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
print_r($stmt->fetchAll(PDO::FETCH_KEY_PAIR));
?>