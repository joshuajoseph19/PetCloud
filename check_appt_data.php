<?php
require_once 'db_connect.php';
$h = $pdo->query("SELECT COUNT(*) FROM hospitals")->fetchColumn();
echo "Hospitals: $h\n";
$s = $pdo->query("SELECT COUNT(*) FROM hospital_services")->fetchColumn();
echo "Services: $s\n";
if ($h > 0) {
    print_r($pdo->query("SELECT * FROM hospitals LIMIT 2")->fetchAll(PDO::FETCH_ASSOC));
}
?>