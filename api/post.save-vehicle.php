<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$json = json_decode(file_get_contents("php://input"));
$vehicle = new Vehicle($json->vehicleId);
$vehicle->color = $json->color;
$vehicle->name = $json->name;
$vehicle->description = $json->description;
$vehicle->passengers = $json->passengers;
// $vehicle->mileage = $json->mileage;
$vehicle->requireCDL = $json->requireCDL ? 1 : 0;
// $vehicle->hasCheckEngine = $json->hasCheckEngine ? 1 : 0;
$result = $vehicle->save();

echo json_encode(['result' => $result]);