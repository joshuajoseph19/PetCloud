<?php
require_once 'db_connect.php';

// Clean out existing and re-initialize with correct names from services table
$pdo->exec("DELETE FROM hospital_services");

$hospitalIds = $pdo->query("SELECT id FROM hospitals")->fetchAll(PDO::FETCH_COLUMN);

// Let's get actual services from DB
$services = $pdo->query("SELECT name FROM services")->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->prepare("INSERT INTO hospital_services (hospital_id, service_name, price, description) VALUES (?, ?, ?, ?)");
$basePrices = [
    'General Checkup' => 500,
    'Medical Consultation' => 650,
    'Vaccination' => 800,
    'Bath & Drying' => 450,
    'Haircut' => 600,
    'Short Stay (Day)' => 1200,
    'Long Stay (Night)' => 2000
];

foreach ($hospitalIds as $hid) {
    foreach ($services as $name) {
        $price = $basePrices[$name] ?? 500;
        $stmt->execute([$hid, $name, $price, "Professional $name for your pet"]);
    }
}
echo "Synced hospital_services with existing services.\n";
?>