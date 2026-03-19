<?php
require_once 'db_connect.php';

echo "<h2>🛠️ Comprehensive Pet Owner Dashboard Repair...</h2>";

try {
    // 1. health_reminders
    $pdo->exec("CREATE TABLE IF NOT EXISTS health_reminders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        due_at DATETIME NOT NULL,
        status ENUM('pending', 'completed', 'deferred') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:blue'>✓ <strong>health_reminders</strong> table verified.</p>";

    // 2. feeding_schedules
    $pdo->exec("CREATE TABLE IF NOT EXISTS feeding_schedules (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_id INT DEFAULT NULL,
        feeding_time TIME NOT NULL,
        days_of_week JSON,
        food_type VARCHAR(100),
        portion_size VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:blue'>✓ <strong>feeding_schedules</strong> table verified.</p>";

    // 3. appointments
    $pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_id INT DEFAULT NULL,
        service_type VARCHAR(100) NOT NULL,
        appointment_date DATE NOT NULL,
        appointment_time TIME NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
        provider_name VARCHAR(255),
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:blue'>✓ <strong>appointments</strong> table verified (recreated if missing).</p>";

    // 4. adoption_listings
    $pdo->exec("CREATE TABLE IF NOT EXISTS adoption_listings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        pet_name VARCHAR(100) NOT NULL,
        pet_type VARCHAR(50) NOT NULL,
        breed VARCHAR(100),
        age VARCHAR(50),
        gender VARCHAR(20),
        description TEXT,
        image_url VARCHAR(255),
        status ENUM('available', 'pending', 'adopted') DEFAULT 'available',
        is_approved TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p style='color:blue'>✓ <strong>adoption_listings</strong> table verified.</p>";

    // 5. Add some demo data for joshuajoseph10310@gmail.com (user 2)
    // First, verify we have pets
    $petStmt = $pdo->query("SELECT id FROM user_pets WHERE user_id = 2 LIMIT 1");
    $pet = $petStmt->fetch();
    if ($pet) {
        $petId = $pet['id'];

        // Add a reminder
        $pdo->prepare("INSERT INTO health_reminders (user_id, title, due_at) VALUES (?, ?, ?)")
            ->execute([2, 'Vaccination Reminder', date('Y-m-d H:i:s', strtotime('+1 day'))]);

        // Add a feeding schedule
        $pdo->prepare("INSERT INTO feeding_schedules (user_id, pet_id, feeding_time, days_of_week) VALUES (?, ?, ?, ?)")
            ->execute([2, $petId, '08:00:00', '["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]']);

        // Add an appointment
        $pdo->prepare("INSERT INTO appointments (user_id, pet_id, service_type, appointment_date, appointment_time) VALUES (?, ?, ?, ?, ?)")
            ->execute([2, $petId, 'Checkup', date('Y-m-d', strtotime('+3 days')), '10:30:00']);

        echo "<p style='color:green'>✓ Added demo dashboard data for owner.</p>";
    } else {
        echo "<p style='color:orange'>⚠️ No pets found for user 2. Please add a pet in 'My Pets' to see full dashboard functionality.</p>";
    }

    echo "<h3>🎉 Pet Owner Dashboard fully repaired! ✨</h3>";

} catch (PDOException $e) {
    echo "<p style='color:red'>CRITICAL ERROR: " . $e->getMessage() . "</p>";
}
?>