<?php
require_once 'db_connect.php';

try {
    // Create shop_applications table
    $sql = "CREATE TABLE IF NOT EXISTS shop_applications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        shop_name VARCHAR(255) NOT NULL,
        shop_category VARCHAR(100) NOT NULL,
        description TEXT,
        status VARCHAR(20) DEFAULT 'pending', -- pending, approved, rejected
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "<h1>✓ Shop Applications Table Created!</h1>";
    echo "<p>Now shop owner requests will show up in the Admin Dashboard.</p>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>