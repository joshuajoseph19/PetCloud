<?php
require_once 'db_connect.php';
$rows = $pdo->query("SELECT name, category_id FROM services")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
?>