<?php
require_once 'db_connect.php';
require_once 'config.php';

echo "<h1>PetCloud Checkout Fixer</h1>";

// 1. Check Database Connection
echo "<h3>1. Checking Database Connection...</h3>";
try {
    $pdo->query("SELECT 1");
    echo "<p style='color: green;'>✅ Connected to database: " . $dbname . "</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Connection failed: " . $e->getMessage() . "</p>";
}

// 2. Check Orders Table
echo "<h3>2. Checking Orders Table...</h3>";
try {
    $pdo->query("SELECT 1 FROM orders LIMIT 1");
    echo "<p style='color: green;'>✅ Orders table exists.</p>";

    // Check payment_id column
    try {
        $pdo->query("SELECT payment_id FROM orders LIMIT 1");
        echo "<p style='color: green;'>✅ 'payment_id' column exists.</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange;'>⚠️ 'payment_id' missing. Fixing now...</p>";
        $pdo->exec("ALTER TABLE orders ADD COLUMN payment_id VARCHAR(255) AFTER user_id");
        echo "<p style='color: green;'>✅ 'payment_id' added successfully.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: orange;'>⚠️ Orders table missing. Running setup...</p>";
    include 'setup_orders_db.php';
    echo "<p style='color: green;'>✅ Setup script completed.</p>";
}

// 3. Check Razorpay Keys
echo "<h3>3. Checking Razorpay Keys...</h3>";
if (defined('RAZORPAY_KEY_ID')) {
    echo "<p style='color: green;'>✅ RAZORPAY_KEY_ID is defined: " . RAZORPAY_KEY_ID . "</p>";
    if (strpos(RAZORPAY_KEY_ID, 'YOUR_') !== false) {
        echo "<p style='color: red;'>❌ ERROR: Your Razorpay Key still contains the placeholder 'YOUR_'. Please update config.php!</p>";
    }
} else {
    echo "<p style='color: red;'>❌ ERROR: RAZORPAY_KEY_ID is NOT defined. Check config.php!</p>";
}

echo "<hr>";
echo "<p><a href='checkout.php'>Go to Checkout Page</a></p>";
?>