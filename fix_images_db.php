<?php
require_once 'db_connect.php';

try {
    $updates = [
        'Premium Dog Food' => 'images/premium_dog_food.webp',
        'Interactive Cat Toy' => 'images/cat_toy.jpg',
        'Comfort Pet Bed' => 'images/Comfort Pet Bed.webp',
        'Bird Seed Mix' => 'images/bird_feed.webp',
        'Chew Bone' => 'images/chew_bone.jpg',
        'Pet Vitamin Supplements' => 'images/Pet Vitamin Supplements.webp',
        'Puppy Food' => 'images/puppy_food.avif'
    ];

    foreach ($updates as $name => $url) {
        $stmt = $pdo->prepare("UPDATE products SET image_url = ? WHERE name = ?");
        $stmt->execute([$url, $name]);
        echo "Updated image for $name to $url\n";
    }

    echo "All product images updated in database.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>