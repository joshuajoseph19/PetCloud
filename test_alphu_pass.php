<?php
require_once 'db_connect.php';
$email = 'alphu@gmail.com';
$testPass = 'admin'; // Or whatever they used?

$stmt = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo "User found. Checking if password is 'admin'...\n";
    if (password_verify($testPass, $user['password_hash'])) {
        echo "YES: Password is 'admin'\n";
    } else {
        echo "NO: Password is NOT 'admin'\n";
        echo "Hash in DB: " . $user['password_hash'] . "\n";
    }
} else {
    echo "User NOT found.\n";
}
?>