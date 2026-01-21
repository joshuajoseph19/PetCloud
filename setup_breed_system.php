<?php
/**
 * Setup Script for Breed System
 */
require_once 'db_connect.php';

try {
    echo "<h1>PetCloud Breed System Setup</h1>";

    $sqlFile = __DIR__ . '/database/breed_schema.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("SQL file not found: " . $sqlFile);
    }

    $sql = file_get_contents($sqlFile);

    // Clean comments
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
                // Ignore "Table/Entry already exists" errors
                if (strpos($e->getMessage(), '1050') === false && strpos($e->getMessage(), '1062') === false) {
                    throw $e;
                }
            }
        }
    }

    $pdo->commit();

    echo "<div style='color: green; padding: 15px; border: 1px solid green; background: #eaffea;'>";
    echo "<h3>✅ Breed System Installed!</h3>";
    echo "<p>Executed $count SQL statements.</p>";
    echo "<ul>";
    echo "<li>Created tables: <strong>adoption_pet_types, breed_categories, adoption_breeds</strong></li>";
    echo "<li>Populated seed data for Dogs, Cats, Birds, Rabbits</li>";
    echo "</ul>";
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