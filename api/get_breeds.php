<?php
/**
 * API Endpoint: Get Breeds by Pet Type
 * Returns breeds filtered by pet type, grouped by breed group
 * 
 * Usage: get_breeds.php?pet_type_id=1
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';

try {
    // Validate parameters
    $petTypeId = isset($_GET['pet_type_id']) ? intval($_GET['pet_type_id']) : null;
    $petTypeSlug = isset($_GET['pet_type']) ? $_GET['pet_type'] : null;

    if (!$petTypeId && !$petTypeSlug) {
        throw new Exception("pet_type_id or pet_type parameter is required");
    }

    // Query to get breeds grouped by breed group (category)
    $query = "SELECT 
                bc.id AS group_id,
                bc.name AS group_name,
                bc.display_order AS group_order,
                ab.id AS breed_id,
                ab.name AS breed_name
              FROM adoption_breeds ab
              JOIN breed_categories bc ON ab.category_id = bc.id ";

    if ($petTypeId) {
        $query .= "WHERE bc.pet_type_id = ? ";
        $params = [$petTypeId];
    } else {
        $query .= "JOIN adoption_pet_types apt ON bc.pet_type_id = apt.id 
                   WHERE apt.slug = ? ";
        $params = [$petTypeSlug];
    }

    $query .= "AND ab.is_active = 1
               ORDER BY bc.display_order, ab.name";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $results = $stmt->fetchAll();

    // Group breeds by breed group
    $groupedBreeds = [];

    foreach ($results as $row) {
        $groupId = $row['group_id'];

        if (!isset($groupedBreeds[$groupId])) {
            $groupedBreeds[$groupId] = [
                'group_id' => (int) $groupId,
                'group_name' => $row['group_name'],
                'group_order' => (int) $row['group_order'],
                'breeds' => []
            ];
        }

        $groupedBreeds[$groupId]['breeds'][] = [
            'id' => (int) $row['breed_id'],
            'name' => $row['breed_name']
        ];
    }

    $breedGroups = array_values($groupedBreeds);
    usort($breedGroups, function ($a, $b) {
        return $a['group_order'] - $b['group_order'];
    });

    $totalBreeds = 0;
    foreach ($breedGroups as $group) {
        $totalBreeds += count($group['breeds']);
    }

    echo json_encode([
        'success' => true,
        'data' => $breedGroups,
        'total_breeds' => $totalBreeds,
        'total_groups' => count($breedGroups)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>