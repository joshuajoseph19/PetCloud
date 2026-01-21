<?php
require_once 'db_connect.php';

try {
    echo "Cleaning up duplicate services...\n\n";

    // Step 1: Find all duplicates (same name + category_id)
    $stmt = $pdo->query("
        SELECT name, category_id, COUNT(*) as count, GROUP_CONCAT(id ORDER BY id) as ids
        FROM services
        GROUP BY name, category_id
        HAVING count > 1
    ");

    $duplicates = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($duplicates)) {
        echo "No duplicates found!\n";
        exit;
    }

    echo "Found " . count($duplicates) . " duplicate service groups:\n\n";

    foreach ($duplicates as $dup) {
        $ids = explode(',', $dup['ids']);
        $keepId = $ids[0]; // Keep the first one (lowest ID)
        $deleteIds = array_slice($ids, 1); // Delete the rest

        echo "Service: {$dup['name']} (Category: {$dup['category_id']})\n";
        echo "  Keeping ID: $keepId\n";
        echo "  Deleting IDs: " . implode(', ', $deleteIds) . "\n";

        // Delete duplicates
        foreach ($deleteIds as $delId) {
            $delStmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
            $delStmt->execute([$delId]);
        }
        echo "  ✓ Cleaned up\n\n";
    }

    echo "\nDone! All duplicates removed.\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>