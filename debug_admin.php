<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, email, role FROM users WHERE role = 'admin'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>