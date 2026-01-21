<?php
/**
 * PetCloud Configuration File
 * Handle API keys and environment settings
 */

// Deployment Environment ('test' or 'live')
define('PAYMENT_MODE', 'test');

// Razorpay Credentials
// Replace these with your actual keys from https://dashboard.razorpay.com/app/keys
if (PAYMENT_MODE === 'live') {
    define('RAZORPAY_KEY_ID', 'rzp_live_YOUR_LIVE_KEY_HERE');
    define('RAZORPAY_KEY_SECRET', 'YOUR_LIVE_KEY_SECRET_HERE');
} else {
    define('RAZORPAY_KEY_ID', 'rzp_test_YOUR_TEST_KEY_HERE');
    define('RAZORPAY_KEY_SECRET', 'YOUR_TEST_KEY_SECRET_HERE');
}

// Database Configuration (already handled in db_connect.php, but good for reference)
// define('DB_HOST', 'localhost');
// define('DB_USER', 'root');
// define('DB_PASS', '');
// define('DB_NAME', 'petcloud');
?>