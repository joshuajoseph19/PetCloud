<?php
require_once 'db_connect.php';

try {
    // 1. Add discount and status to products if missing
    $pdo->exec("ALTER TABLE products ADD COLUMN discount DECIMAL(5,2) DEFAULT 0.00 AFTER price");
    $pdo->exec("ALTER TABLE products ADD COLUMN status ENUM('active', 'inactive') DEFAULT 'active' AFTER stock");
} catch (Exception $e) {
}

try {
    // 2. Notifications table for shop owners
    $pdo->exec("CREATE TABLE IF NOT EXISTS shop_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_id INT NOT NULL,
        type VARCHAR(50),
        message TEXT,
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
}

try {
    // 3. Reviews table (Product reviews)
    $pdo->exec("CREATE TABLE IF NOT EXISTS product_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_id INT NOT NULL,
        rating INT DEFAULT 5,
        comment TEXT,
        reply TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
}

try {
    // 4. Shop Settings / Profile (Already using shop_applications but let's ensure more fields)
    $pdo->exec("ALTER TABLE shop_applications ADD COLUMN shop_logo VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE shop_applications ADD COLUMN shop_address TEXT DEFAULT NULL");
    $pdo->exec("ALTER TABLE shop_applications ADD COLUMN bank_details TEXT DEFAULT NULL");
} catch (Exception $e) {
}

// --- SEED DUMMY DATA ---
try {
    // Get a shop_id (first approved)
    $shop_id = $pdo->query("SELECT id FROM shop_applications WHERE status = 'approved' LIMIT 1")->fetchColumn();
    if ($shop_id) {
        // Clear old ones to avoid duplicates on rerun if needed, or just insert
        // Notifications
        $pdo->prepare("INSERT INTO shop_notifications (shop_id, type, message) VALUES (?, 'order', 'You have a new order for Premium Dog Food.')")->execute([$shop_id]);

        // Reviews
        $pid = $pdo->query("SELECT id FROM products WHERE shop_id = $shop_id LIMIT 1")->fetchColumn();
        if ($pid) {
            $pdo->prepare("INSERT INTO product_reviews (product_id, user_id, rating, comment) VALUES (?, 1, 5, 'Amazing quality, my retriever is very happy!')")->execute([$pid]);
        }
    }
} catch (Exception $e) {
}

echo "Shop Owner database schema updated and seeded successfully.";
?>