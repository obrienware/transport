<?php
header('Content-Type: application/json');
require_once 'class.sms.php';

$result = SMS::optIn($_REQUEST['phone']);

echo json_encode(['result' => $result]);