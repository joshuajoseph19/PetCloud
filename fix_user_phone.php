<?php
require_once 'db_connect.php';

try {
    // Add phone column to users table if missing
    $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) DEFAULT NULL AFTER email");
    echo "Successfully added 'phone' column to 'users' table.<br>";
} catch (Exception $e) {
    echo "Error or Column already exists: " . $e->getMessage() . "<br>";
}

try {
    // Also ensure profile_pic column exists as it might be used in header
    $pdo->exec("ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL AFTER phone");
    echo "Successfully adjusted 'profile_pic' column.<br>";
} catch (Exception $e) {
}

echo "User schema fix complete.";
?>