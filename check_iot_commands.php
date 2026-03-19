<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT COUNT(*) FROM feed_commands WHERE status='pending'");
$pending = $stmt->fetchColumn();
echo "Pending commands: " . $pending . "\n";
?>
