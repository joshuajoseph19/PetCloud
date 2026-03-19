<?php
require_once 'db_connect.php';
$table = $_GET['table'] ?? 'appointments';
echo "Schema for table: $table\n";
try {
    $stmt = $pdo->query("DESCRIBE $table");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>