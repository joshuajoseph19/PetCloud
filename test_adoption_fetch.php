<?php
require_once 'db_connect.php';
$sql = "SELECT * FROM adoption_listings WHERE status = 'active' ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$listings = $stmt->fetchAll();
echo "Number of active listings: " . count($listings) . "\n";
foreach ($listings as $l) {
    echo "- " . $l['pet_name'] . " (ID: " . $l['id'] . ")\n";
}
?>