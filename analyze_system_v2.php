<?php
require_once 'db_connect.php';

$outputFile = 'analysis_results.txt';
$fp = fopen($outputFile, 'w');

function logMsg($msg)
{
    global $fp;
    echo $msg . "\n";
    fwrite($fp, $msg . "\n");
}

logMsg("--- PetCloud System Analysis ---");
logMsg("Timestamp: " . date('Y-m-d H:i:s'));

try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    logMsg("Total tables found: " . count($tables));

    $statusStmt = $pdo->query("SHOW TABLE STATUS");
    $statuses = $statusStmt->fetchAll();

    $broken = [];
    $healthy = [];
    $nullEngine = [];

    foreach ($statuses as $row) {
        $name = $row['Name'];
        $engine = $row['Engine'];

        if ($engine === null) {
            $nullEngine[] = $name;
            continue;
        }

        try {
            $pdo->query("SELECT 1 FROM `$name` LIMIT 1");
            $healthy[] = $name;
        } catch (PDOException $e) {
            $broken[] = "$name (Error: " . $e->getMessage() . ")";
        }
    }

    logMsg("\n[SUMMARY]");
    logMsg("Healthy Tables: " . count($healthy));
    logMsg("Tables with NULL Engine (Corrupt): " . count($nullEngine));
    logMsg("Broken Tables (Accessible but query failed): " . count($broken));

    if (!empty($nullEngine)) {
        logMsg("\n[CORRUPT TABLES - NULL ENGINE]");
        foreach ($nullEngine as $t)
            logMsg(" - $t");
    }

    if (!empty($broken)) {
        logMsg("\n[BROKEN TABLES]");
        foreach ($broken as $t)
            logMsg(" - $t");
    }

} catch (Exception $e) {
    logMsg("CRITICAL ERROR: " . $e->getMessage());
}

fclose($fp);
echo "\nResults written to analysis_results.txt\n";
