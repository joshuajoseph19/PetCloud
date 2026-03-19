<?php
require_once 'db_connect.php';
$_SESSION['user_id'] = 2; // Joshua
ob_start();
include 'adoption.php';
$html = ob_get_clean();
if (strpos($html, 'akku') !== false) {
    echo "SUCCESS: 'akku' found in adoption.php output.\n";
} else {
    echo "FAILURE: 'akku' NOT found in adoption.php output.\n";
    echo "First 500 chars of HTML: \n" . substr($html, 0, 500) . "\n";
}
?>