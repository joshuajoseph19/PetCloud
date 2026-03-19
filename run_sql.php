<?php
require_once 'db_connect.php';
try {
    $sql = file_get_contents('setup_adoption_v2.sql');
    // Split into individual queries to handle potential errors in middle
    $queries = explode(';', $sql);
    foreach ($queries as $q) {
        $q = trim($q);
        if (empty($q))
            continue;
        try {
            $pdo->exec($q);
            echo "SUCCESS: " . substr($q, 0, 50) . "...\n";
        } catch (Exception $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }
} catch (Exception $e) {
    echo "CRITICAL: " . $e->getMessage() . "\n";
}
