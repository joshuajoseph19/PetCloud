<?php
require_once 'db_connect.php';

try {
    echo "--- Recreating Users Table ---\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("DROP TABLE IF EXISTS users");
    echo "Dropped users table\n";

    $sql = "CREATE TABLE IF NOT EXISTS `users` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `full_name` varchar(255) NOT NULL,
      `email` varchar(255) NOT NULL UNIQUE,
      `password_hash` varchar(255) NOT NULL,
      `google_id` varchar(255) DEFAULT NULL,
      `profile_pic` varchar(500) DEFAULT NULL,
      `role` varchar(20) DEFAULT 'client',
      `location` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `role` (`role`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $pdo->exec($sql);
    echo "Created users table\n";

    // Insert admin user
    $adminEmail = 'admin@gmail.com';
    $passHash = password_hash('admin', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES ('System Administrator', ?, ?, 'admin')");
    $stmt->execute([$adminEmail, $passHash]);
    echo "Admin user created: admin@gmail.com / admin\n";

    // Insert a sample user for testing
    $userEmail = 'joshuajoseph10310@gmail.com';
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, role) VALUES ('Joshua Joseph', ?, ?, 'client')");
    $stmt->execute([$userEmail, $passHash]);
    echo "Sample user created: $userEmail / admin\n";

    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "\n--- Success! ---\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
