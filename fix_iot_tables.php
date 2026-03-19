<?php
require_once 'db_connect.php';
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS feed_commands (
        id INT AUTO_INCREMENT PRIMARY KEY,
        device_id VARCHAR(50) NOT NULL,
        portion_qty INT NOT NULL,
        status ENUM('pending', 'done') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "feed_commands table verified/created.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS feed_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        device_id VARCHAR(50) NOT NULL,
        `portion` INT NOT NULL,
        fed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "feed_logs table verified/created.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
