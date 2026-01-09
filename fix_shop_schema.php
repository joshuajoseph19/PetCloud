<?php
require_once 'db_connect.php';

echo "<h2>🛠️ fixing Shop Database Schema...</h2>";

try {
    // 1. Add missing columns to shop_applications
    $columns = [
        "phone VARCHAR(20)",
        "password_hash VARCHAR(255)",
        "business_reg VARCHAR(100)",
        "address TEXT",
        "years_in_business INT"
    ];

    foreach ($columns as $colDef) {
        $colName = explode(' ', $colDef)[0];
        try {
            // Try to select the column to see if it exists
            $pdo->query("SELECT $colName FROM shop_applications LIMIT 1");
            echo "<p style='color:green'>✓ Column <strong>$colName</strong> already exists.</p>";
        } catch (PDOException $e) {
            // Column doesn't exist, ignore error and try to add it
            try {
                $sql = "ALTER TABLE shop_applications ADD COLUMN $colDef";
                $pdo->exec($sql);
                echo "<p style='color:blue'>+ Added column <strong>$colName</strong>.</p>";
            } catch (PDOException $e2) {
                echo "<p style='color:red'>! Failed to add $colName: " . $e2->getMessage() . "</p>";
            }
        }
    }

    echo "<h3>🎉 Database upgrade complete!</h3>";
    echo "<p>You can now submit the <a href='shopowner-apply.php'>Shop Owner Application</a> and it will work correctly.</p>";

} catch (PDOException $e) {
    echo "CRITICAL ERROR: " . $e->getMessage();
}
?>