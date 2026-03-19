<?php
require_once 'db_connect.php';

function ensure_column($pdo, $table, $column, $definition)
{
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM $table LIKE '$column'");
        if (!$stmt->fetch()) {
            $pdo->exec("ALTER TABLE $table ADD COLUMN $column $definition");
            echo "Added $column to $table.\n";
        }
    } catch (Exception $e) {
        echo "Error ensuring $column in $table: " . $e->getMessage() . "\n";
    }
}

try {
    // 1. daily_tasks
    $pdo->exec("CREATE TABLE IF NOT EXISTS daily_tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        task_name VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    ensure_column($pdo, 'daily_tasks', 'task_time', 'VARCHAR(100)');
    ensure_column($pdo, 'daily_tasks', 'task_date', 'DATE');
    ensure_column($pdo, 'daily_tasks', 'frequency', "ENUM('Once', 'Daily', 'Weekly', 'Monthly') DEFAULT 'Once'");
    ensure_column($pdo, 'daily_tasks', 'is_done', 'TINYINT(1) DEFAULT 0');

    // 2. health_records
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

    // 3. pet_memories
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

    echo "Health system repair logic applied. ✨\n";

} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage() . "\n";
}
?>