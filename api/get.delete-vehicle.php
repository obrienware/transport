<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$result = Vehicle::delete($_REQUEST['id']);
die(json_encode(['result' => $result]));