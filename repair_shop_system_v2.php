<?php
require_once 'db_connect.php';

echo "<h2>🛍️ Repairing Shop System Database (v2)...</h2>";

try {
    // 1. Repair products table
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'shop_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE products ADD COLUMN shop_id INT NOT NULL AFTER id");
        echo "<p style='color:blue'>+ Added <strong>shop_id</strong> to <strong>products</strong>.</p>";
    }

    // Add status column
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'status'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE products ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        echo "<p style='color:blue'>+ Added <strong>status</strong> to <strong>products</strong>.</p>";
    }

    // Add discount column
    $stmt = $pdo->query("SHOW COLUMNS FROM products LIKE 'discount'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE products ADD COLUMN discount INT DEFAULT 0");
        echo "<p style='color:blue'>+ Added <strong>discount</strong> to <strong>products</strong>.</p>";
    }

    // 2. Ensure orders table exists and has proper columns
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status ENUM('Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Add missing order columns if needed
    $orderCols = [
        "payment_method VARCHAR(50)",
        "address TEXT",
        "phone VARCHAR(20)"
    ];
    foreach ($orderCols as $colDef) {
        $colName = explode(' ', $colDef)[0];
        try {
            $pdo->query("SELECT $colName FROM orders LIMIT 1");
        } catch (PDOException $e) {
            $pdo->exec("ALTER TABLE orders ADD COLUMN $colDef");
            echo "<p style='color:blue'>+ Added <strong>$colName</strong> to <strong>orders</strong>.</p>";
        }
    }

    // 3. Ensure order_items table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        price_at_purchase DECIMAL(10,2) NOT NULL
    )");

    echo "<h3>🎉 Shop system database repair complete! ✨</h3>";

} catch (PDOException $e) {
    echo "<p style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</p>";
}
?>