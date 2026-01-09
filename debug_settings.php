<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT * FROM system_settings");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>