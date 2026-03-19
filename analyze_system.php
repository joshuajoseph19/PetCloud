<?php
require_once 'db_connect.php';

header('Content-Type: text/plain');

echo "--- PetCloud System Analysis ---\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Check Tables Status
    echo "Checking database tables...\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $broken_tables = [];
    $healthy_tables = [];

    foreach ($tables as $table) {
        try {
            // Run CHECK TABLE
            $check = $pdo->query("CHECK TABLE `$table`")->fetch();
            $msg_type = $check['Msg_type'];
            $msg_text = $check['Msg_text'];

            // Try to select
            $countStmt = $pdo->query("SELECT COUNT(*) FROM `$table` LIMIT 1");
            $count = $countStmt->fetchColumn();

            if ($msg_type === 'status' && $msg_text === 'OK') {
                $healthy_tables[] = "$table (Rows: $count)";
            } else {
                $broken_tables[] = "$table - Check result: $msg_type: $msg_text";
            }
        } catch (PDOException $e) {
            $broken_tables[] = "$table - Error: " . $e->getMessage();
        }
    }

    echo "\nHealthy Tables (" . count($healthy_tables) . "):\n";
    foreach ($healthy_tables as $t)
        echo " - $t\n";

    echo "\nBroken/Missing Tables (" . count($broken_tables) . "):\n";
    foreach ($broken_tables as $t)
        echo " - $t\n";

    // 2. Check Specific Error from Screenshot (health_reminders)
    echo "\nSpecific Check: health_reminders\n";
    if (in_array('health_reminders', $tables)) {
        echo "Table 'health_reminders' exists in list.\n";
    } else {
        echo "Table 'health_reminders' NOT found in list.\n";
    }

    // 3. Check for .frm files vs InnoDB
    // This is hard via PHP/SQL if it's an engine error, but SHOW TABLE STATUS might help.
    echo "\nTable Storage Engine Status:\n";
    $status = $pdo->query("SHOW TABLE STATUS")->fetchAll();
    foreach ($status as $row) {
        if ($row['Engine'] === null) {
            echo " ! WARNING: {$row['Name']} has NULL engine (Possible corruption)\n";
        } else {
            // echo " - {$row['Name']}: {$row['Engine']}\n";
        }
    }

} catch (Exception $e) {
    echo "CRITICAL ERROR: " . $e->getMessage() . "\n";
}
