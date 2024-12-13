<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.vehicle.php';
$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
$previousName = $vehicle->name;

$vehicle->color = $json->color;
$vehicle->name = $json->name;
$vehicle->description = $json->description;
$vehicle->licensePlate = $json->licensePlate;
$vehicle->passengers = $json->passengers;
$vehicle->requireCDL = $json->requireCDL ? 1 : 0;

$result = $vehicle->save();
if ($json->vehicleId) {
  $before = $vehicle->getState();
  $id = $json->vehicleId;
  $action = 'modified';
  $description = 'Changed vehicle: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added vehicle: '.$json->name;
}
$vehicle->getVehicle($id);
$after = $vehicle->getState();
Audit::log($action, 'vehicles', $description, $before, $after);

echo json_encode(['result' => $result]);