<?php
require_once 'db_connect.php';
$s = $pdo->query("DESCRIBE users");
while ($r = $s->fetch(PDO::FETCH_ASSOC)) {
    print_r($r);
}
