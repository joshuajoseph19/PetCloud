<?php
/**
 * Setup Script for Service Types Expansion
 * Run this once to create the new tables and populate seed data.
 */

require_once 'db_connect.php';

try {
    echo "<h1>PetCloud Service Expansion Setup</h1>";

    // 1. Read the SQL file
    $sqlFile = __DIR__ . '/database/services_schema_expansion.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: " . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);

    // 2. Execute queries
    // Split by semicolon to handle multiple statements if PDO doesn't support multiple at once depending on config
    // However, usually it's safer to separate.

    echo "<p>Reading SQL file...</p>";

    // Remove comments
    $lines = explode("\n", $sql);
    $cleanSql = "";
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, "--") && !str_starts_with($line, "#")) {
            $cleanSql .= $line . "\n";
        }
    }

    $statements = explode(";", $cleanSql);

    $pdo->beginTransaction();

    $count = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $pdo->exec($statement);
                $count++;
            } catch (PDOException $e) {
                // Ignore "Table already exists" errors to allow re-running
                if (strpos($e->getMessage(), '1050') === false) {
                    throw $e;
                }
            }
        }
    }

    $pdo->commit();

    echo "<div style='color: green; padding: 15px; border: 1px solid green; background: #eaffea;'>";
    echo "<h3>✅ Success!</h3>";
    echo "<p>Executed $count SQL statements.</p>";
    echo "<ul>";
    echo "<li>Created tables: <strong>service_categories, services, service_pet_type_compatibility, clinic_services</strong></li>";
    echo "<li>Populated seed data: <strong>8 Categories, 40+ Services</strong></li>";
    echo "</ul>";
    echo "<p>You can now delete this file or keep it for reference.</p>";
    echo "</div>";

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<div style='color: red; padding: 15px; border: 1px solid red; background: #ffeaea;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>