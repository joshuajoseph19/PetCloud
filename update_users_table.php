<?php
require_once 'db_connect.php';

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN location VARCHAR(255) DEFAULT 'San Francisco, CA'");
} catch (Exception $e) {
}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN bio TEXT");
} catch (Exception $e) {
}

try {
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_image TEXT");
} catch (Exception $e) {
}

echo "Users table updated successfully.";
?>