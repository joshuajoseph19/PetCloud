<?php
require_once 'db_connect.php';
$stmt = $pdo->query("DESCRIBE appointments");
echo "Appointments Columns:\n";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
}

echo "\nHospitals Table:\n";
try {
    $stmt = $pdo->query("DESCRIBE hospitals");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>