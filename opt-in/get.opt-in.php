<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\SMS;

$result = SMS::optIn($_GET['phone']);

echo json_encode(['result' => $result]);