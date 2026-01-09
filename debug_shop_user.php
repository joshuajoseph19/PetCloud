<?php
require_once 'db_connect.php';
$stmt = $pdo->prepare("SELECT id, email, role FROM users WHERE email = ?");
$stmt->execute(['testshop@gmail.com']);
print_r($stmt->fetch(PDO::FETCH_ASSOC));
?>