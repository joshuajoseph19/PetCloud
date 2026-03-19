<?php
require_once 'db_connect.php';

echo "Repairing Smart Feeder Infrastructure...\n";

// 1. Create feed_commands Table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS feed_commands (
        id INT AUTO_INCREMENT PRIMARY KEY,
        device_id VARCHAR(50) NOT NULL,
        portion_qty INT NOT NULL,
        status ENUM('pending', 'completed') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "- Table 'feed_commands' verified.\n";
} catch (PDOException $e) {
    echo "Error creating feed_commands: " . $e->getMessage() . "\n";
}

// 2. Create feed_logs Table
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS feed_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        device_id VARCHAR(50) NOT NULL,
        `portion` INT NOT NULL,
        fed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "- Table 'feed_logs' verified.\n";
} catch (PDOException $e) {
    echo "Error creating feed_logs: " . $e->getMessage() . "\n";
}

echo "Infrastructure Ready!\n";
?>