<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT * FROM service_categories");
echo "Count: " . $stmt->rowCount() . "\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>