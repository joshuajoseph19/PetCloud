<?php
require_once 'db_connect.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS adoption_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_name VARCHAR(100) NOT NULL,
        pet_category VARCHAR(50),
        applicant_name VARCHAR(255) NOT NULL,
        applicant_email VARCHAR(255) NOT NULL,
        applicant_phone VARCHAR(20),
        reason_for_adoption TEXT,
        living_situation VARCHAR(100), -- House, Apartment, etc.
        has_other_pets TINYINT(1),
        status VARCHAR(20) DEFAULT 'pending', -- pending, approved, rejected
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id)
    )";

    $pdo->exec($sql);
    echo "Adoption applications table created successfully.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>