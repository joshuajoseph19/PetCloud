<?php
require_once 'db_connect.php';

try {
    // 1. Admin Roles Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_roles (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        permissions TEXT,
        user_count INT DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default roles if empty
    $checkRoles = $pdo->query("SELECT COUNT(*) FROM admin_roles")->fetchColumn();
    if ($checkRoles == 0) {
        $pdo->exec("INSERT INTO admin_roles (name, permissions, user_count) VALUES 
            ('Super Admin', 'Full System Access', 1),
            ('Platform Moderator', 'Content, Users, Shops', 2),
            ('Support Agent', 'View Only, Adoption Review', 3)");
    }

    // 2. System Settings Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_key VARCHAR(100) PRIMARY KEY,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");

    // Seed default settings
    $defaultSettings = [
        'commission_rate' => '10',
        'min_payout' => '50',
        'tax_rate' => '2.5',
        'maintenance_mode' => '0',
        'auto_approve_shops' => '0',
        'public_adoption' => '1'
    ];

    foreach ($defaultSettings as $key => $val) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $val]);
    }

    // 3. Platform Notifications Table (Check/Create)
    $pdo->exec("CREATE TABLE IF NOT EXISTS platform_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        target_role VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Admin functionality database tables initialized successfully! ✅";

} catch (PDOException $e) {
    echo "Error initializing admin tables: " . $e->getMessage();
}
?>