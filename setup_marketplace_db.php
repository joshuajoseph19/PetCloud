<?php
require_once 'db_connect.php';

try {
    // 1. Products Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        category VARCHAR(100),
        image_url TEXT,
        stock INT DEFAULT 100,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Cart Table (Simplified)
    $pdo->exec("CREATE TABLE IF NOT EXISTS cart (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT DEFAULT 1,
        added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )");

    // 3. Seed Products if empty
    $count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    if ($count == 0) {
        $sql = "INSERT INTO products (name, description, price, category, image_url) VALUES 
            ('Premium Dog Food', 'Premium Chicken & Rice - Large Breed (3kg)', 2499.00, 'Food', 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=600'),
            ('Puppy Food', 'Healthy Growth Formula - Chicken & Milk (3kg)', 849.00, 'Food', 'https://images.unsplash.com/photo-1589924691195-41432c84c161?w=600'),
            ('Interactive Cat Toy', 'Smart Laser & Motion Sensor Toy', 499.00, 'Toys', 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=600'),
            ('Comfort Pet Bed', 'Orthopedic Foam Pet Bed - Washable', 2899.00, 'Accessories', 'https://images.unsplash.com/photo-1591584250171-04144f87da1e?w=600'),
            ('Bird Seed Mix', 'Premium Mix Seeds for Small/Medium Birds (1kg)', 349.00, 'Food', 'https://images.unsplash.com/photo-1551969014-7d2c4da3d4f7?w=600'),
            ('Chew Bone', 'Durable Rubber Chew Bone (Medium)', 199.00, 'Toys', 'https://images.unsplash.com/photo-1544568100-847a948585b9?w=600'),
            ('Pet Vitamin Supplements', 'Multivitamin Soft Chews (60 count)', 399.00, 'Health', 'https://images.unsplash.com/photo-1583336663277-620dd17319e3?w=600')";
        $pdo->exec($sql);
    }

    echo "Marketplace database setup complete!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>