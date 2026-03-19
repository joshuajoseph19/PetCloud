<?php
require_once 'db_connect.php';

try {
    echo "Repairing daily_tasks...\n";
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

    // Check for frequency column specifically
    $stmt = $pdo->query("SHOW COLUMNS FROM daily_tasks LIKE 'frequency'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE daily_tasks ADD COLUMN frequency ENUM('Once', 'Daily', 'Weekly', 'Monthly') DEFAULT 'Once'");
        echo "Added frequency to daily_tasks.\n";
    }

    echo "Ensuring health_records exists...\n";
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

    echo "Ensuring pet_memories exists...\n";
    $pdo->exec("CREATE TABLE IF NOT EXISTS pet_memories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        image_path VARCHAR(255),
        memory_date DATE NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    echo "Health system repair complete! ✨\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>