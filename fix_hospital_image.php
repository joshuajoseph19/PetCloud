<?php
require_once 'db_connect.php';

try {
    // Update image for Vet 24x7 Emergency to use the local uploaded image
    $newImage = 'images/vet_emergency.png';

    $stmt = $pdo->prepare("UPDATE hospitals SET image_url = ? WHERE name LIKE ?");
    $stmt->execute([$newImage, '%Vet 24x7%']);

    echo "Updated 'Vet 24x7 Emergency' image to local file successfully.";

} catch (PDOException $e) {
    echo "Update failed: " . $e->getMessage();
}
?>