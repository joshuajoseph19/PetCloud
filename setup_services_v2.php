<?php
require_once 'db_connect.php';

function runSQL($pdo, $sql, $msg)
{
    try {
        $pdo->exec($sql);
        echo "[SUCCESS] $msg\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), '1050') !== false) {
            echo "[INFO] Table already exists: $msg\n";
        } else {
            echo "[ERROR] $msg: " . $e->getMessage() . "\n";
        }
    }
}

echo "--- Starting Services Setup ---\n";

// 1. Create Tables
$sql_cat = "CREATE TABLE IF NOT EXISTS service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(100) DEFAULT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
runSQL($pdo, $sql_cat, "Create service_categories");

$sql_svc = "CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    default_duration_minutes INT DEFAULT 30,
    is_medical TINYINT(1) DEFAULT 0,
    is_home_service_supported TINYINT(1) DEFAULT 0,
    is_clinic_service_supported TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
runSQL($pdo, $sql_svc, "Create services");


// 2. Insert Data
try {
    $pdo->beginTransaction();
    echo "--- Inserting Data ---\n";

    // Categories
    $cats = [
        ['Medical Consultation', 'medical', 'fa-user-md', 1],
        ['Preventive Care', 'preventive', 'fa-shield-virus', 2],
        ['Grooming & Spa', 'grooming', 'fa-pump-soap', 3],
        ['Diagnostics', 'diagnostics', 'fa-microscope', 4],
        ['Surgery & Dental', 'surgery', 'fa-syringe', 5],
        ['Alternative Therapy', 'therapy', 'fa-spa', 6],
        ['Training & Behavior', 'training', 'fa-graduation-cap', 7],
        ['Boarding & Daycare', 'boarding', 'fa-home', 8]
    ];

    $stmt = $pdo->prepare("INSERT IGNORE INTO service_categories (name, slug, icon, display_order) VALUES (?, ?, ?, ?)");
    foreach ($cats as $c) {
        $stmt->execute($c);
    }

    // Services
    // Helper to get cat id
    function getCId($pdo, $slug)
    {
        $stmt = $pdo->prepare("SELECT id FROM service_categories WHERE slug=?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn();
    }

    $c_med = getCId($pdo, 'medical');
    $c_prev = getCId($pdo, 'preventive');
    $c_groom = getCId($pdo, 'grooming');
    // ... add others if needed, focusing on main ones

    $stmtSvc = $pdo->prepare("INSERT IGNORE INTO services (category_id, name, default_duration_minutes, is_medical) VALUES (?, ?, ?, ?)");

    if ($c_med) {
        $stmtSvc->execute([$c_med, 'General Checkup', 20, 1]);
        $stmtSvc->execute([$c_med, 'Emergency Consultation', 30, 1]);
    }
    if ($c_prev) {
        $stmtSvc->execute([$c_prev, 'Vaccination', 15, 1]);
    }
    if ($c_groom) {
        $stmtSvc->execute([$c_groom, 'Full Grooming', 90, 0]);
        $stmtSvc->execute([$c_groom, 'Bath & Dry', 45, 0]);
    }

    $pdo->commit();
    echo "[SUCCESS] Service Data Inserted!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "[ERROR] Insert Failed: " . $e->getMessage() . "\n";
}
?>