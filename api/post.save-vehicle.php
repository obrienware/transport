<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
$vehicle->color = $json->color;
$vehicle->name = $json->name;
$vehicle->description = $json->description;
$vehicle->licensePlate = $json->licensePlate;
$vehicle->passengers = $json->passengers;
$vehicle->requireCDL = $json->requireCDL ? 1 : 0;

if ($vehicle->save()) {
  $result = $vehicle->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));