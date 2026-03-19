<?php
session_start();
$_SESSION['user_id'] = 2; // Joshua
$_SESSION['user_role'] = 'client';
$_SESSION['user_name'] = 'Joshua';
require_once 'db_connect.php';

// Bypass session_start in adoption.php if it's already started
$code = file_get_contents('adoption.php');
$code = str_replace('session_start();', '// session_start();', $code);
eval ('?>' . $code);

$html = ob_get_clean();
if (strpos($html, 'akku') !== false) {
    echo "SUCCESS: 'akku' found in adoption.php output.\n";
} else {
    echo "FAILURE: 'akku' NOT found in adoption.php output.\n";
    echo "Count of active listings in DB: " . $pdo->query("SELECT COUNT(*) FROM adoption_listings WHERE status='active'")->fetchColumn() . "\n";
    echo "First 500 chars of HTML: \n" . substr($html, 0, 500) . "\n";
}
?>