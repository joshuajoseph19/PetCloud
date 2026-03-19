<?php
require_once 'db_connect.php';

try {
    // 1. Drop the corrupted table and start fresh to be safe
    echo "Dropping and recreating daily_tasks...\n";
    $pdo->exec("DROP TABLE IF EXISTS daily_tasks");
    $pdo->exec("CREATE TABLE daily_tasks (
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

    echo "Daily tasks table recreated successfully. ✨\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>