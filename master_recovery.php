<?php
require_once 'db_connect.php';

echo "--- PetCloud Master Recovery Script ---\n";

// 1. Get all NULL engine tables
$stmt = $pdo->query("SHOW TABLE STATUS");
$statuses = $stmt->fetchAll();
$toDrop = [];
foreach ($statuses as $row) {
    if ($row['Engine'] === null && $row['Name'] !== 'users') {
        $toDrop[] = $row['Name'];
    }
}

echo "Found " . count($toDrop) . " broken tables to drop.\n";

// 2. Drop them
foreach ($toDrop as $table) {
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$table` CASCADE");
        echo "Dropped $table\n";
    } catch (Exception $e) {
        echo "Failed to drop $table: " . $e->getMessage() . "\n";
    }
}

// 3. Drop dependent tables manually if any foreign key issues persist
// (MySQL often handles this with the DROP, but sometimes you need to disable FK checks)
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// 4. Run Setup Files (The ones we know work)
$setupFiles = [
    'setup_user_pets_db.php',
    'setup_functionality.php',
    'database/setup_lost_pet.php',
    'database/setup_general_found.php',
    'setup_appointment_system.php',
    'setup_breed_system_v2.php',
    'setup_services_v2.php',
    'setup_marketplace_db.php',
    'setup_orders_db.php',
    'setup_shop_db.php',
    'setup_feeding_db_v2.php',
    'setup_smart_feeder_db.php'
];

foreach ($setupFiles as $file) {
    $absPath = __DIR__ . '/' . $file;
    if (file_exists($absPath)) {
        echo "\nExecuting setup: $file\n";
        // Use output buffering to capture output
        ob_start();
        include $absPath;
        $output = ob_get_clean();
        echo "Result: " . strip_tags($output) . "\n";
    } else {
        echo "Skipping missing file: $file\n";
    }
}

// 5. Run SQL files
$sqlFiles = [
    'database/pet_rehoming_schema.sql',
    'database/smart_feeder_setup.sql'
];

foreach ($sqlFiles as $file) {
    $absPath = __DIR__ . '/' . $file;
    if (file_exists($absPath)) {
        echo "\nExecuting SQL: $file\n";
        $sql = file_get_contents($absPath);
        // Basic split for multiple statements (Note: might fail on complex SQL, but works for most)
        $queries = explode(';', $sql);
        foreach ($queries as $q) {
            $q = trim($q);
            if (!empty($q)) {
                try {
                    $pdo->exec($q);
                } catch (Exception $e) {
                    echo "Query Error in $file: " . $e->getMessage() . "\n";
                }
            }
        }
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "\n--- Recovery Complete! ---\n";
echo "Please check the dashboard now.\n";
