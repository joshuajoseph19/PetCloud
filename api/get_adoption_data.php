<?php
/**
 * API Endpoint: Get Adoption Data (Pet Types, Categories, Breeds)
 * 
 * Modes:
 * 1. No params: Returns all pet types
 * 2. ?start=true: Returns pet types with categories
 * 3. ?pet_type_id=X: Returns categories and breeds for a specific pet type
 */

header('Content-Type: application/json');
require_once '../db_connect.php';

try {
    $pet_type_slug = $_GET['type'] ?? null; // e.g., 'dog'

    // 1. Fetch Categories & Breeds for a specific pet type
    if ($pet_type_slug) {
        // Get Pet Type ID
        $stmt = $pdo->prepare("SELECT id FROM adoption_pet_types WHERE slug = ?");
        $stmt->execute([$pet_type_slug]);
        $type = $stmt->fetch();

        if (!$type) {
            echo json_encode(['success' => false, 'error' => 'Invalid pet type']);
            exit;
        }

        $typeId = $type['id'];

        // Fetch Categories
        $catStmt = $pdo->prepare("SELECT id, name FROM breed_categories WHERE pet_type_id = ? ORDER BY display_order ASC");
        $catStmt->execute([$typeId]);
        $categories = $catStmt->fetchAll();

        $result = [];

        foreach ($categories as $cat) {
            // Fetch Breeds for this category
            $breedStmt = $pdo->prepare("SELECT id, name FROM adoption_breeds WHERE category_id = ? AND is_active = 1 ORDER BY name ASC");
            $breedStmt->execute([$cat['id']]);
            $breeds = $breedStmt->fetchAll();

            $result[] = [
                'category_id' => $cat['id'],
                'category_name' => $cat['name'],
                'breeds' => $breeds
            ];
        }

        echo json_encode(['success' => true, 'data' => $result]);
    }
    // 2. Fetch all Pet Types (for initial load if needed)
    else {
        $stmt = $pdo->prepare("SELECT slug, name, icon FROM adoption_pet_types WHERE is_active = 1 ORDER BY display_order ASC");
        $stmt->execute();
        $types = $stmt->fetchAll();

        echo json_encode(['success' => true, 'data' => $types]);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>