<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';

echo "--- PetCloud Master Recovery Script v3 ---\n";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");

// 1. Get all tables
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo "Found " . count($tables) . " tables in database.\n";

foreach ($tables as $table) {
    if ($table === 'users')
        continue;
    try {
        $pdo->exec("DROP TABLE IF EXISTS `$table` CASCADE");
        echo "[OK] Dropped $table\n";
    } catch (Exception $e) {
        echo "[FAIL] Drop $table: " . $e->getMessage() . "\n";
    }
}

echo "\n--- Recreating Tables ---\n";

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

$root = __DIR__;

foreach ($setupFiles as $file) {
    $absPath = $root . '/' . $file;
    if (file_exists($absPath)) {
        echo "\n> Running $file ... ";
        try {
            $dir = dirname($absPath);
            chdir($dir); // Change to the file's directory so relative paths work

            ob_start();
            include $absPath;
            $res = ob_get_clean();
            echo "Done.\n" . strip_tags($res) . "\n";
        } catch (Throwable $e) {
            if (ob_get_level() > 0)
                ob_get_clean();
            echo "FAILED: " . $e->getMessage() . "\n";
        }
        chdir($root); // Change back
    }
}

$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "\n--- Process Finished ---\n";
