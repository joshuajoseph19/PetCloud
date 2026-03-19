<?php
require_once 'db_connect.php';
try {
    $count = $pdo->exec("UPDATE users SET email = TRIM(email)");
    echo "Successfully trimmed $count emails in the database.\n";

    // Also reset the password for joshuajoseph10310@gmail.com one last time
    $email = 'joshuajoseph10310@gmail.com';
    $password = 'admin';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    echo "Password for $email reset to 'admin'.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
