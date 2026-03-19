<?php
require_once 'db_connect.php';

echo "<h2>🛠️ Repairing Shop Notifications & Owner Dashboard...</h2>";

try {
    // 1. Create shop_notifications table
    $pdo->exec("CREATE TABLE IF NOT EXISTS shop_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        shop_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT,
        is_read TINYINT(1) DEFAULT 0,
        type VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:blue'>+ Created <strong>shop_notifications</strong> table.</p>";

    // 2. Add some demo notifications for the shop owner
    // Get the first approved shop for testing (alphu shop)
    $stmt = $pdo->query("SELECT id FROM shop_applications WHERE status = 'approved' LIMIT 1");
    $shop = $stmt->fetch();
    if ($shop) {
        $pdo->prepare("INSERT INTO shop_notifications (shop_id, title, message) VALUES (?, ?, ?)")
            ->execute([$shop['id'], 'Welcome aboard!', 'Your shop is now active. Start adding products to get orders!']);
        echo "<p style='color:green'>✓ Added demo notification for shop.</p>";
    }

    // 3. Check for pet_owners (clients) tables
    // The previous summary mentions 'user_pets' table exists.
    // Let's ensure other relevant tables for pet owners exist.

    // a. daily_tasks (re-re-ensure)
    $pdo->exec("CREATE TABLE IF NOT EXISTS daily_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_id INT DEFAULT NULL,
        task_name VARCHAR(255) NOT NULL,
        task_time VARCHAR(100),
        task_date DATE,
        frequency ENUM('Once', 'Daily', 'Weekly', 'Monthly') DEFAULT 'Once',
        is_done TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // b. pet_health_logs (if dashboard uses a different name?)
    // Actually health-records.php uses 'health_records'
    $pdo->exec("CREATE TABLE IF NOT EXISTS health_records (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_id INT NOT NULL,
        record_type VARCHAR(100) NOT NULL,
        record_date DATE NOT NULL,
        description TEXT,
        document_path VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "<h3>🎉 Owner Dashboard & Shop Notifications repaired! ✨</h3>";

} catch (PDOException $e) {
    echo "<p style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</p>";
}
?>