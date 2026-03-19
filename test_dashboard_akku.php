<?php
session_start();
$_SESSION['user_id'] = 2; // Joshua
$_SESSION['user_role'] = 'client';
$_SESSION['user_name'] = 'Joshua';
require_once 'db_connect.php';

$code = file_get_contents('dashboard.php');
$code = str_replace('session_start();', '// session_start();', $code);
ob_start();
eval ('?>' . $code);
$html = ob_get_clean();

if (strpos($html, 'akku') !== false) {
    echo "SUCCESS: 'akku' found in dashboard.php output for User 2.\n";
} else {
    echo "FAILURE: 'akku' NOT found in dashboard.php output for User 2.\n";
    echo "Check if status is 'active' and user_id is not 2.\n";
}
?>