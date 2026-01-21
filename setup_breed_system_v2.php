<?php
// Setup Script V2 - Robust DDL & Data Insertion
require_once 'db_connect.php';

function runSQL($pdo, $sql, $msg)
{
    try {
        $pdo->exec($sql);
        echo "[SUCCESS] $msg\n";
    } catch (PDOException $e) {
        // Ignore "Table already exists" (1050)
        if (strpos($e->getMessage(), '1050') !== false) {
            echo "[INFO] Table already exists: $msg\n";
        } else {
            echo "[ERROR] $msg: " . $e->getMessage() . "\n";
        }
    }
}

echo "--- Starting Breed System Setup ---\n";

// 1. Create Tables (No Transaction for DDL)
$sql_types = "CREATE TABLE IF NOT EXISTS adoption_pet_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    icon VARCHAR(50) DEFAULT 'fa-paw',
    display_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
runSQL($pdo, $sql_types, "Create adoption_pet_types");

$sql_cats = "CREATE TABLE IF NOT EXISTS breed_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pet_type_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (pet_type_id) REFERENCES adoption_pet_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
runSQL($pdo, $sql_cats, "Create breed_categories");

$sql_breeds = "CREATE TABLE IF NOT EXISTS adoption_breeds (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES breed_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
runSQL($pdo, $sql_breeds, "Create adoption_breeds");

// 2. Insert Data (Transactional)
try {
    $pdo->beginTransaction();
    echo "--- Inserting Data ---\n";

    // Clear old data to prevent dupes (Optional: strictly for reset)
    // $pdo->exec("SET FOREIGN_KEY_CHECKS = 0; TRUNCATE TABLE adoption_breeds; TRUNCATE TABLE breed_categories; TRUNCATE TABLE adoption_pet_types; SET FOREIGN_KEY_CHECKS = 1;");

    // Insert Pet Types (IGnore if exists updates)
    $pdo->exec("INSERT INTO adoption_pet_types (name, slug, icon, display_order) VALUES
    ('Dog', 'dog', 'fa-dog', 1),
    ('Cat', 'cat', 'fa-cat', 2),
    ('Bird', 'bird', 'fa-dove', 3),
    ('Rabbit', 'rabbit', 'fa-carrot', 4)
    ON DUPLICATE KEY UPDATE icon=VALUES(icon);");

    // Helper to get ID
    function getId($pdo, $slug)
    {
        $stmt = $pdo->prepare("SELECT id FROM adoption_pet_types WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetchColumn();
    }

    $dog_id = getId($pdo, 'dog');
    $cat_id = getId($pdo, 'cat');
    $bird_id = getId($pdo, 'bird');

    if ($dog_id) {
        // Insert Categories
        $cats = ['Sporting Group', 'Herding Group', 'Toy Group', 'Mixed/Other'];
        foreach ($cats as $c) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO breed_categories (pet_type_id, name) VALUES (?, ?)");
            $stmt->execute([$dog_id, $c]);
        }

        // Insert Breeds
        // Get Cat IDs
        $c_sport = $pdo->query("SELECT id FROM breed_categories WHERE name='Sporting Group' AND pet_type_id=$dog_id")->fetchColumn();
        if ($c_sport) {
            $pdo->exec("INSERT IGNORE INTO adoption_breeds (category_id, name) VALUES ($c_sport, 'Golden Retriever'), ($c_sport, 'Labrador Retriever')");
        }
    }

    // Add logic for Cats
    if ($cat_id) {
        $cats = ['Short Hair', 'Long Hair'];
        foreach ($cats as $c) {
            $pdo->prepare("INSERT IGNORE INTO breed_categories (pet_type_id, name) VALUES (?, ?)")->execute([$cat_id, $c]);
        }
        // Get Cat IDs
        $c_short = $pdo->query("SELECT id FROM breed_categories WHERE name='Short Hair' AND pet_type_id=$cat_id")->fetchColumn();
        if ($c_short) {
            $pdo->exec("INSERT IGNORE INTO adoption_breeds (category_id, name) VALUES ($c_short, 'Domestic Short Hair'), ($c_short, 'British Shorthair')");
        }
    }

    $pdo->commit();
    echo "[SUCCESS] Data Inserted!\n";

} catch (Exception $e) {
    if ($pdo->inTransaction())
        $pdo->rollBack();
    echo "[ERROR] Data Insertion Failed: " . $e->getMessage() . "\n";
}
?>