<?php
require_once 'db_connect.php';

try {
    // 1. Add status to users table
    $pdo->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS status VARCHAR(20) DEFAULT 'active'");
    echo "Added 'status' column to users table. ✅<br>";

    // 2. Ensure system_settings is correct
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
    echo "Verified 'system_settings' table. ✅<br>";

    // 3. Ensure admin_roles is correct
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        permissions TEXT,
        user_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Verified 'admin_roles' table. ✅<br>";

    // 4. Ensure platform_notifications is correct
    $pdo->exec("CREATE TABLE IF NOT EXISTS platform_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        target_role VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "Verified 'platform_notifications' table. ✅<br>";

} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
?>