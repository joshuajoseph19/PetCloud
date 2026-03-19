<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SHOW TABLES LIKE 'feed_logs'");
if ($stmt->fetch()) {
    echo "feed_logs table EXISTS\n";
    $stmt2 = $pdo->query("SELECT * FROM feed_logs ORDER BY id DESC LIMIT 1");
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo "Last log: " . json_encode($row) . "\n";
    } else {
        echo "Table is empty\n";
    }
} else {
    echo "feed_logs table DOES NOT EXIST\n";
}
?>
