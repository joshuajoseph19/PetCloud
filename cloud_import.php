<?php
// Aiven Connection Details (Obtained from screenshot)
$host = getenv('DB_HOST');
$port = getenv('DB_PORT');
$user = getenv('DB_USER');
$pass = getenv('DB_PASSWORD');
$db = getenv('DB_NAME');

try {
    echo "Connecting to Aiven MySQL Instance...\n";
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully!\n";

    // Use the file path provided by user
    $filename = 'database/petcloud_db.sql';

    if (!file_exists($filename)) {
        die("Error: Could not find '$filename'. Please ensure the file exists.\n");
    }

    echo "Reading file: $filename ...\n";
    $sql = file_get_contents($filename);

    // Remove CREATE DATABASE and USE statements to ensure it works on Aiven's defaultdb
    $sql = preg_replace('/CREATE DATABASE IF NOT EXISTS `?\w+`?.*;/i', '', $sql);
    $sql = preg_replace('/USE `?\w+`?;/i', '', $sql);

    echo "Importing data to Aiven (this may take a few moments)...\n";

    // Disable primary key requirement for this session (Aiven default is ON)
    $pdo->exec("SET SESSION sql_require_primary_key = 0");

    // Split by semicolon to execute one by one if it's very large, 
    // but for 79KB exec($sql) should be fine.
    $pdo->exec($sql);

    echo "----------------------------------------------------------\n";
    echo "SUCCESS! Your database tables have been created on Aiven.\n";
    echo "----------------------------------------------------------\n";
    echo "You can now safely delete this file (cloud_import.php).\n";

}
catch (PDOException $e) {
    echo "----------------------------------------------------------\n";
    echo "DATABASE ERROR: " . $e->getMessage() . "\n";
    echo "----------------------------------------------------------\n";
}
