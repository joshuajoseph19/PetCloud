<?php
require_once 'db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS user_pets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_name VARCHAR(100) NOT NULL,
        pet_breed VARCHAR(100),
        pet_age VARCHAR(50),
        pet_type VARCHAR(50), -- Dog, Cat, Bird, etc.
        pet_image TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

    $pdo->exec($sql);

    // Seed a default pet if none exists for a user (optional, but good for demo)
    // We'll handle this in mypets.php instead.

    echo "User pets table created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>