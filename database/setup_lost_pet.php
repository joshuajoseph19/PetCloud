<?php
require_once '../db_connect.php';

try {
    // 1. Add status to user_pets if it doesn't exist
    try {
        $pdo->exec("ALTER TABLE user_pets ADD COLUMN status ENUM('Active', 'Lost') DEFAULT 'Active'");
        echo "Column 'status' added to 'user_pets'.<br>";
    } catch (PDOException $e) {
        echo "Column 'status' already exists or error: " . $e->getMessage() . "<br>";
    }

    // 2. Create lost_pet_alerts table
    $sql_alerts = "CREATE TABLE IF NOT EXISTS lost_pet_alerts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pet_id INT NOT NULL,
        user_id INT NOT NULL,
        last_seen_location VARCHAR(255) NOT NULL,
        last_seen_date DATE NOT NULL,
        description TEXT,
        status ENUM('Active', 'Resolved') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (pet_id) REFERENCES user_pets(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql_alerts);
    echo "Table 'lost_pet_alerts' created successfully.<br>";

    // 3. Create found_pet_reports table
    $sql_reports = "CREATE TABLE IF NOT EXISTS found_pet_reports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        alert_id INT NOT NULL,
        user_id INT NOT NULL,
        found_location VARCHAR(255) NOT NULL,
        found_date DATE NOT NULL,
        notes TEXT,
        contact_info VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (alert_id) REFERENCES lost_pet_alerts(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql_reports);
    echo "Table 'found_pet_reports' created successfully.<br>";

    echo "<strong>Lost Pet Alert System database setup complete!</strong>";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>