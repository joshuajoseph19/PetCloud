<?php
require_once 'db_connect.php';

try {
    // 1. Create Hospitals Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hospitals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        image_url VARCHAR(255),
        contact_number VARCHAR(20),
        rating DECIMAL(2,1) DEFAULT 4.5,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // 2. Create Hospital Services (Pricing) Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS hospital_services (
        id INT AUTO_INCREMENT PRIMARY KEY,
        hospital_id INT NOT NULL,
        service_name VARCHAR(50) NOT NULL,
        price DECIMAL(10,2) NOT NULL,
        description TEXT,
        FOREIGN KEY (hospital_id) REFERENCES hospitals(id) ON DELETE CASCADE,
        UNIQUE(hospital_id, service_name)
    )");

    // 3. Update Appointments Table
    // Check if column exists first
    $stmt = $pdo->query("SHOW COLUMNS FROM appointments LIKE 'hospital_id'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE appointments ADD COLUMN hospital_id INT NULL AFTER user_id");
    }

    $stmt = $pdo->query("SHOW COLUMNS FROM appointments LIKE 'status'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE appointments ADD COLUMN status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed'");
    }

    // 4. Seed Data
    // Check if hospitals exist
    $stmt = $pdo->query("SELECT COUNT(*) FROM hospitals");
    if ($stmt->fetchColumn() == 0) {
        $hospitals = [
            [
                'name' => 'PetCare City Clinic',
                'address' => 'Indiranagar, Bangalore',
                'image_url' => 'https://images.unsplash.com/photo-1584132967334-10e028bd69f7?w=500',
                'rating' => 4.8
            ],
            [
                'name' => 'Happy Paws Veterinary',
                'address' => 'Koramangala, Bangalore',
                'image_url' => 'https://images.unsplash.com/photo-1532938911079-1b06ac7ceec7?w=500',
                'rating' => 4.6
            ],
            [
                'name' => 'Vet 24x7 Emergency',
                'address' => 'Whitefield, Bangalore',
                'image_url' => 'https://images.unsplash.com/photo-1599443015574-be5fe85b3b49?w=500',
                'rating' => 4.9
            ]
        ];

        $insertHospital = $pdo->prepare("INSERT INTO hospitals (name, address, image_url, rating) VALUES (?, ?, ?, ?)");
        $insertService = $pdo->prepare("INSERT INTO hospital_services (hospital_id, service_name, price) VALUES (?, ?, ?)");

        foreach ($hospitals as $h) {
            $insertHospital->execute([$h['name'], $h['address'], $h['image_url'], $h['rating']]);
            $hId = $pdo->lastInsertId();

            // Add Services for each
            $insertService->execute([$hId, 'Checkup', rand(400, 800)]); // ₹400 - ₹800
            $insertService->execute([$hId, 'Grooming', rand(1000, 2500)]); // ₹1000 - ₹2500
            $insertService->execute([$hId, 'Vaccine', rand(500, 1200)]); // ₹500 - ₹1200
        }
        echo "Seeded " . count($hospitals) . " hospitals with services successfully.<br>";
    } else {
        echo "Hospitals already exist. Skipping seed.<br>";
    }

    echo "Database setup completed safely.";

} catch (PDOException $e) {
    echo "Setup failed: " . $e->getMessage();
}
?>