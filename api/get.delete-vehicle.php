<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$vehicle = new Vehicle($_REQUEST['id']);
$result = $vehicle->delete();
die(json_encode(['result' => $result]));