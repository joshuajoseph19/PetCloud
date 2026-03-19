<?php
require_once 'db_connect.php';
$email = 'alphu@gmail.com';

echo "Email: $email\n";

$s = $pdo->prepare("SELECT password_hash FROM users WHERE email = ?");
$s->execute([$email]);
$u = $s->fetch();
if ($u)
    echo "User Hash: " . $u['password_hash'] . "\n";

$s = $pdo->prepare("SELECT password_hash FROM shop_applications WHERE email = ?");
$s->execute([$email]);
$a = $s->fetch();
if ($a)
    echo "App Hash:  " . $a['password_hash'] . "\n";

if ($u && $a && $u['password_hash'] === $a['password_hash']) {
    echo "Hashes MATCH correctly.\n";
} else if ($u && $a) {
    echo "Hashes DON'T MATCH! User has something else.\n";
}
?>