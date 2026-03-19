<?php
require_once 'db_connect.php';
$stmt = $pdo->query("SELECT id, user_id, pet_name, status, pet_type FROM adoption_listings");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Adoption Listings:\n";
foreach ($rows as $r) {
    echo "- ID: {$r['id']}, User: {$r['user_id']}, Name: {$r['pet_name']}, Status: {$r['status']}, Type: {$r['pet_type']}\n";
}
?>