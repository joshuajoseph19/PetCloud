<?php
require_once 'db_connect.php';
$stmt = $pdo->query("DESCRIBE appointments");
echo "Appointments Full Column List:\n";
$cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "- " . $c['Field'] . "\n";
}
?>