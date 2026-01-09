<?php
require_once 'db_connect.php';

echo "<h2>🛠️ Updating Products Table...</h2>";

try {
    // Check if shop_id exists
    try {
        $pdo->query("SELECT shop_id FROM products LIMIT 1");
        echo "<p style='color:green'>✓ Column <strong>shop_id</strong> already exists.</p>";
    } catch (PDOException $e) {
        $sql = "ALTER TABLE products ADD COLUMN shop_id INT DEFAULT NULL";
        $pdo->exec($sql);
        echo "<p style='color:blue'>+ Added column <strong>shop_id</strong> to products table.</p>";
    }

    echo "<h3>🎉 Update complete!</h3>";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>