<?php
require_once 'db_connect.php';
$email = 'joshuajoseph10310@gmail.com';
$pass = 'admin';

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo "User found: " . $user['email'] . "\n";
    if (password_verify($pass, $user['password_hash'])) {
        echo "Password verification: SUCCESS\n";
    } else {
        echo "Password verification: FAILED\n";
        echo "Hash: " . $user['password_hash'] . "\n";
    }
} else {
    echo "User not found: $email\n";
}
