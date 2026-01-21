<?php
require_once '../db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS general_found_pets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        reporter_id INT NOT NULL,
        pet_type VARCHAR(50),
        pet_breed VARCHAR(100),
        found_location VARCHAR(255) NOT NULL,
        found_date DATE NOT NULL,
        description TEXT,
        contact_info VARCHAR(255),
        pet_image TEXT,
        status ENUM('Active', 'Resolved') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (reporter_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql);
    echo "Table 'general_found_pets' created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>