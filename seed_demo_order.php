<?php
require_once 'db_connect.php';

try {
    $pdo->beginTransaction();

    // 1. Create a dummy order from a client (User ID 1)
    $pdo->exec("INSERT INTO orders (user_id, total_amount, status) VALUES (1, 59.98, 'Pending')");
    $orderId = $pdo->lastInsertId();

    // 2. Add an item to the order (Product ID 1, which now belongs to Shop ID 1)
    $pdo->exec("INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES ($orderId, 1, 1, 59.98)");

    $pdo->commit();
    echo "Demo order created for shop verification!";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "Error: " . $e->getMessage();
}
?>