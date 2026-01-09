<?php
require_once 'db_connect.php';

try {
    $products = [
        [
            'name' => 'Premium Dog Food',
            'description' => 'Premium Chicken & Rice - Large Breed (3kg)',
            'price' => 2499.00,
            'category' => 'Food',
            'image_url' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?w=600'
        ],
        [
            'name' => 'Puppy Food',
            'description' => 'Healthy Growth Formula - Chicken & Milk (3kg)',
            'price' => 849.00,
            'category' => 'Food',
            'image_url' => 'https://images.unsplash.com/photo-1589924691195-41432c84c161?w=600'
        ],
        [
            'name' => 'Interactive Cat Toy',
            'description' => 'Smart Laser & Motion Sensor Toy',
            'price' => 499.00,
            'category' => 'Toys',
            'image_url' => 'https://images.unsplash.com/photo-1545249390-6bdfa286032f?w=600'
        ],
        [
            'name' => 'Comfort Pet Bed',
            'description' => 'Orthopedic Foam Pet Bed - Washable',
            'price' => 2899.00,
            'category' => 'Accessories',
            'image_url' => 'https://images.unsplash.com/photo-1591584250171-04144f87da1e?w=600'
        ],
        [
            'name' => 'Bird Seed Mix',
            'description' => 'Premium Mix Seeds for Small/Medium Birds (1kg)',
            'price' => 349.00,
            'category' => 'Food',
            'image_url' => 'https://images.unsplash.com/photo-1551969014-7d2c4da3d4f7?w=600'
        ],
        [
            'name' => 'Chew Bone',
            'description' => 'Durable Rubber Chew Bone (Medium)',
            'price' => 199.00,
            'category' => 'Toys',
            'image_url' => 'https://images.unsplash.com/photo-1544568100-847a948585b9?w=600'
        ],
        [
            'name' => 'Pet Vitamin Supplements',
            'description' => 'Multivitamin Soft Chews (60 count)',
            'price' => 399.00,
            'category' => 'Health',
            'image_url' => 'https://images.unsplash.com/photo-1583336663277-620dd17319e3?w=600'
        ]
    ];

    echo "Updating product prices...\n";

    foreach ($products as $p) {
        // Check if exists
        $stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
        $stmt->execute([$p['name']]);
        $exists = $stmt->fetch();

        if ($exists) {
            // Update
            $sql = "UPDATE products SET price = ?, description = ?, image_url = ?, category = ? WHERE name = ?";
            $pdo->prepare($sql)->execute([$p['price'], $p['description'], $p['image_url'], $p['category'], $p['name']]);
            echo "Updated: " . $p['name'] . " to ₹" . $p['price'] . "\n";
        } else {
            // Insert
            $sql = "INSERT INTO products (name, description, price, category, image_url) VALUES (?, ?, ?, ?, ?)";
            $pdo->prepare($sql)->execute([$p['name'], $p['description'], $p['price'], $p['category'], $p['image_url']]);
            echo "Inserted: " . $p['name'] . " at ₹" . $p['price'] . "\n";
        }
    }

    echo "All prices updated successfully for realistic Indian market rates.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>