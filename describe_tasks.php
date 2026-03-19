<?php
require_once 'db_connect.php';
$stmt = $pdo->query("DESCRIBE daily_tasks");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
}
?>