<?php
require_once 'db_connect.php';

try {
    echo "<h1>Database Repair Tool</h1>";

    // 1. Add 'role' column if missing
    try {
        $pdo->query("SELECT role FROM users LIMIT 1");
        echo "<p style='color:green'>✓ 'role' column already exists.</p>";
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'client'");
        echo "<p style='color:green'>✓ Fixed: Added missing 'role' column.</p>";
    }

    // 2. Add 'google_id' column if missing
    try {
        $pdo->query("SELECT google_id FROM users LIMIT 1");
        echo "<p style='color:green'>✓ 'google_id' column already exists.</p>";
    } catch (PDOException $e) {
        $pdo->exec("ALTER TABLE users ADD COLUMN google_id VARCHAR(255) DEFAULT NULL");
        echo "<p style='color:green'>✓ Fixed: Added missing 'google_id' column.</p>";
    }

    // 3. Create Admin Account
    $adminEmail = 'admin@gmail.com';
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);

    if ($stmt->rowCount() == 0) {
        $passHash = password_hash('admin', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES ('System Administrator', ?, ?, 'admin')";
        $pdo->prepare($sql)->execute([$adminEmail, $passHash]);
        echo "<p style='color:green'>✓ Admin account created successfully.</p>";
    } else {
        // Ensure role is admin
        $pdo->prepare("UPDATE users SET role = 'admin' WHERE email = ?")->execute([$adminEmail]);
        // Reset password to 'admin' just in case
        $passHash = password_hash('admin', PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?")->execute([$passHash, $adminEmail]);
        echo "<p style='color:green'>✓ Admin account updated/verified.</p>";
    }

    echo "<hr><h3>🎉 Success! Database is fully fixed.</h3>";
    echo "<p>You can now <a href='signup.php'>Go back to Signup</a> or <a href='index.php'>Login</a>.</p>";

} catch (PDOException $e) {
    echo "<h3 style='color:red'>Error: " . $e->getMessage() . "</h3>";
}
?>