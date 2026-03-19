<?php
require_once 'db_connect.php';
$email = 'joshuajoseph10310@gmail.com';
$pass = 'admin';

echo "<h2>Debug Login Test</h2>";
echo "Attempting to find user: $email<br>";

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user) {
    echo "SUCCESS: User found in database.<br>";
    echo "Checking password 'admin'...<br>";
    if (password_verify($pass, $user['password_hash'])) {
        echo "<b>SUCCESS: Password verification passed!</b><br>";
        echo "<br>If index.php still fails, make sure you are not entering extra spaces in the email/password fields on the login page.";
    } else {
        echo "<b>FAILED: Password verification failed.</b><br>";
        echo "Database Hash: " . $user['password_hash'] . "<br>";
        $newHash = password_hash($pass, PASSWORD_DEFAULT);
        echo "Expected Hash (example): " . $newHash . "<br>";
    }
} else {
    echo "<b>FAILED: User not found in database.</b><br>";
}
?>