<?php
require_once 'db_connect.php';
$stmt = $pdo->query("DESCRIBE hospital_services");
echo "hospital_services Columns:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>