<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, email, role FROM users WHERE id = 4");
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>