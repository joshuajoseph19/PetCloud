<?php
require_once 'db_connect.php';
function desc($pdo, $table)
{
    echo "========================================\n";
    echo "Schema for: $table\n";
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']} - {$row['Default']}\n";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
desc($pdo, 'appointments');
desc($pdo, 'hospitals');
desc($pdo, 'hospital_services');
?>