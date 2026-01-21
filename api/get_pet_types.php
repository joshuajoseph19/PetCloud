<?php
/**
 * API Endpoint: Get All Active Pet Types
 * Returns list of pet types for dropdown selection
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db_connect.php';

try {
    $query = "SELECT id, name, icon 
              FROM adoption_pet_types 
              WHERE is_active = 1 
              ORDER BY display_order, name";

    $stmt = $pdo->query($query);
    $petTypes = $stmt->fetchAll();

    echo json_encode([
        'success' => true,
        'data' => $petTypes,
        'count' => count($petTypes)
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>