<?php
require_once 'db_connect.php';

echo "Starting Appointment System Repair...\n";

// 1. Repair appointments table
try {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN hospital_id INT AFTER payment_id");
    echo "- Added hospital_id to appointments\n";
} catch (PDOException $e) {
}

try {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN pet_name VARCHAR(100) AFTER hospital_id");
    echo "- Added pet_name to appointments\n";
} catch (PDOException $e) {
}

try {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN breed VARCHAR(100) AFTER pet_name");
    echo "- Added breed to appointments\n";
} catch (PDOException $e) {
}

try {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN title VARCHAR(255) AFTER service_type");
    echo "- Added title to appointments\n";
} catch (PDOException $e) {
}

try {
    $pdo->exec("ALTER TABLE appointments ADD COLUMN cost DECIMAL(10,2) AFTER description");
    echo "- Added cost to appointments\n";
} catch (PDOException $e) {
}

// Rename 'notes' to 'description' if 'description' is missing and 'notes' exists
try {
    $pdo->query("SELECT description FROM appointments LIMIT 1");
} catch (PDOException $e) {
    try {
        $pdo->exec("ALTER TABLE appointments CHANGE notes description TEXT");
        echo "- Renamed notes to description in appointments\n";
    } catch (PDOException $ex) {
        $pdo->exec("ALTER TABLE appointments ADD COLUMN description TEXT AFTER appointment_time");
        echo "- Added description to appointments\n";
    }
}

// 2. Initialize service_categories if empty
$count = $pdo->query("SELECT COUNT(*) FROM service_categories")->fetchColumn();
if ($count == 0) {
    $categories = [
        ['Medical Care', 'medical', 'fa-stethoscope', 'Veterinary checkups and treatments', 1],
        ['Grooming', 'grooming', 'fa-scissors', 'Spa and grooming services', 2],
        ['Pet Boarding', 'boarding', 'fa-hotel', 'Safe home away from home', 3],
        ['Training', 'training', 'fa-graduation-cap', 'Professional obedience training', 4],
        ['Pet Sitting', 'sitting', 'fa-house-user', 'In-home pet care', 5],
        ['Walking', 'walking', 'fa-dog', 'Daily exercise and walks', 6],
        ['Photography', 'photography', 'fa-camera', 'Professional pet portraits', 7],
        ['Nutrition', 'nutrition', 'fa-bowl-food', 'Diet and nutrition consulting', 8]
    ];

    $stmt = $pdo->prepare("INSERT INTO service_categories (name, slug, icon, description, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
    foreach ($categories as $cat) {
        $stmt->execute($cat);
    }
    echo "- Initialized service_categories\n";
}

// 3. Initialize hospitals if empty
$count = $pdo->query("SELECT COUNT(*) FROM hospitals")->fetchColumn();
if ($count == 0) {
    $hospitals = [
        ['City Pet Clinic', '123 Pet Lane, Downtown', 'images/hosp1.png', '4.8'],
        ['Healthy Paws Hospital', '456 Fur Avenue, Westside', 'images/hosp2.png', '4.6'],
        ['Whiskers & Wag Clinic', '789 Tail Road, Eastside', 'images/hosp3.png', '4.9']
    ];

    $stmt = $pdo->prepare("INSERT INTO hospitals (name, address, image_url, rating) VALUES (?, ?, ?, ?)");
    foreach ($hospitals as $h) {
        $stmt->execute($h);
    }
    echo "- Initialized hospitals\n";
}

// 4. Initialize hospital_services if empty (link clinics to categories/services)
$count = $pdo->query("SELECT COUNT(*) FROM hospital_services")->fetchColumn();
if ($count == 0) {
    $hospitalIds = $pdo->query("SELECT id FROM hospitals")->fetchAll(PDO::FETCH_COLUMN);

    // Services we want to offer
    $services = [
        ['General Checkup', 500, 'Basic health examination'],
        ['Vaccination', 800, 'Core vaccinations for pets'],
        ['Dental Cleaning', 1500, 'Professional teeth cleaning'],
        ['Emergency Care', 2000, 'Immediate medical attention']
    ];

    $stmt = $pdo->prepare("INSERT INTO hospital_services (hospital_id, service_name, price, description) VALUES (?, ?, ?, ?)");
    foreach ($hospitalIds as $hid) {
        foreach ($services as $s) {
            $stmt->execute([$hid, $s[0], $s[1], $s[2]]);
        }
    }
    echo "- Initialized hospital_services\n";
}

echo "Repair Complete!\n";
?>