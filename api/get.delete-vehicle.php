<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.vehicle.php';
$vehicle = new Vehicle($_REQUEST['id']);
$before = $vehicle->getState();
$description = 'Deleted vehicle: '.$vehicle->name;
Audit::log('deleted', 'vehicles', $description, $before);
$result = $vehicle->delete();
die(json_encode(['result' => $result]));