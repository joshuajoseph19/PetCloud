<?php
require_once 'db_connect.php';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM adoption_applications");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in adoption_applications: " . implode(', ', $columns) . "\n";

    // Check if listing_id exists, if not add it
    if (!in_array('listing_id', $columns)) {
        echo "Adding listing_id column...\n";
        $pdo->exec("ALTER TABLE adoption_applications ADD COLUMN listing_id INT DEFAULT NULL AFTER user_id");
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>