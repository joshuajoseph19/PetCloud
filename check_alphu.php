<?php
require_once 'db_connect.php';
$email = 'alphu@gmail.com';

echo "Checking $email...\n";

$stmt = $pdo->prepare("SELECT * FROM shop_applications WHERE email = ?");
$stmt->execute([$email]);
$app = $stmt->fetch();
if ($app) {
    echo "Application Status: " . $app['status'] . "\n";
} else {
    echo "No application found for this email.\n";
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();
if ($user) {
    echo "User exists in 'users' table with role: " . $user['role'] . "\n";
} else {
    echo "User DOES NOT exist in 'users' table yet.\n";
}
?>