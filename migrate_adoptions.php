<?php
// migrate_adoptions.php
// Migrates legacy adoption listings to the new pet_rehoming_listings table
// Run from CLI or Browser

require_once 'db_connect.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Starting migration...\n";

try {
    // 1. Get all legacy listings that are NOT already migrated (conceptually)
    // We don't have a specific valid 'migrated' flag, so we might duplicate if run multiple times.
    // For safety, let's check for duplicates based on pet_name + user_id + created_at similarity unique key if possible
    // or just simply migrate everything and user can delete duplicates.
    // Better: Check if (user_id, pet_name) exists in target.

    $stmt = $pdo->query("SELECT * FROM adoption_listings");
    $legacyListings = $stmt->fetchAll();

    $count = 0;
    $skipped = 0;

    // Cache for lookups
    $petTypes = [];
    $ptStmt = $pdo->query("SELECT id, LOWER(name) as name, LOWER(slug) as slug FROM adoption_pet_types"); // Or pet_types
    while ($row = $ptStmt->fetch()) {
        $petTypes[$row['name']] = $row['id'];
        if ($row['slug'])
            $petTypes[$row['slug']] = $row['id'];
    }
    // Hardcode fallback just in case
    $petTypes['dog'] = 1;
    $petTypes['cat'] = 2;
    $petTypes['bird'] = 3;
    $petTypes['rabbit'] = 4;

    echo "Found " . count($legacyListings) . " legacy listings.\n";

    foreach ($legacyListings as $legacy) {
        $userId = $legacy['user_id'];
        $petName = $legacy['pet_name'];
        if (empty($petName))
            continue;

        // Check for duplicates
        $checkStmt = $pdo->prepare("SELECT id FROM pet_rehoming_listings WHERE user_id = ? AND pet_name = ?");
        $checkStmt->execute([$userId, $petName]);
        if ($checkStmt->fetch()) {
            echo "Skipping '$petName' (already exists).\n";
            $skipped++;
            continue;
        }

        // Map Pet Type
        $legacyType = strtolower(trim($legacy['pet_type']));
        $petTypeId = $petTypes[$legacyType] ?? 1; // Default to Dog if unknown

        // Map Breed
        // Try to find breed ID by name
        $breedId = null;
        if (!empty($legacy['breed'])) {
            $breedName = trim($legacy['breed']);
            // Try explicit match
            $bStmt = $pdo->prepare("SELECT id FROM breeds WHERE name = ? AND pet_type_id = ?");
            $bStmt->execute([$breedName, $petTypeId]);
            $bRow = $bStmt->fetch();
            if ($bRow) {
                $breedId = $bRow['id'];
            } else {
                // Try partial match or just leave null
                // Maybe create it? Nah, better leave null or 'Unknown'.
                // If the target table requires breed_id, we might need a fallback.
                // Assuming nullable based on previous describe results.
            }
        }

        // Map Status
        $status = 'Pending';
        switch ($legacy['status']) {
            case 'active':
                $status = 'Approved';
                break;
            case 'adopted':
                $status = 'Adopted';
                break;
            case 'rejected':
                $status = 'Rejected';
                break;
            default:
                $status = 'Pending';
        }

        // Fetch User Location for City/State
        $userStmt = $pdo->prepare("SELECT location FROM users WHERE id = ?");
        $userStmt->execute([$userId]);
        $userLoc = $userStmt->fetchColumn();

        $location = $userLoc ?: 'Unknown Location';
        $city = 'Unknown';
        $state = 'Unknown';

        // Simple location parser (assumes "City, State" format)
        if ($location && strpos($location, ',') !== false) {
            $parts = explode(',', $location);
            $city = trim($parts[0]);
            $state = trim($parts[1] ?? 'Unknown');
        } else {
            $city = $location;
        }

        // Insert
        $insertSql = "INSERT INTO pet_rehoming_listings (
            user_id, pet_type_id, breed_id, pet_name, 
            age_years, gender, size, color, 
            is_vaccinated, is_neutered, 
            reason_for_rehoming, adoption_fee, 
            location, city, state, 
            primary_image, status, created_at
        ) VALUES (
            ?, ?, ?, ?, 
            ?, ?, ?, ?, 
            ?, ?, 
            ?, ?, 
            ?, ?, ?, 
            ?, ?, ?
        )";

        $stmt = $pdo->prepare($insertSql);
        $stmt->execute([
            $userId,
            $petTypeId,
            $breedId,
            $petName,
            intval($legacy['age']), // age_years
            $legacy['gender'] ?? 'Unknown',
            'Medium', // Default size
            'Unknown', // Default color
            ($legacy['vaccination_status'] == 'Vaccinated' ? 1 : 0),
            0, // Default not neutered
            $legacy['reason_for_adoption'] ?: ($legacy['description'] ?: 'Rehoming'),
            0.00, // Default fee
            $location,
            $city,
            $state,
            $legacy['image_url'],
            $status,
            $legacy['created_at']
        ]);

        $count++;
        echo "Migrated '$petName'.\n";
    }

    echo "Migration complete. Migrated: $count, Skipped: $skipped.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>