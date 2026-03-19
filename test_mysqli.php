<?php
require_once 'api/db.php';
$stmt = $conn->query("SELECT * FROM feed_logs");
echo "Count: " . $stmt->num_rows . "\n";
?>