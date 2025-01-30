<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);

switch ($json->name) {
  case 'fuel':
    $vehicle->fuelLevel = $json->value;
    break;
  case 'mileage':
    $vehicle->mileage = $json->value;
    break;
  default:
    echo json_encode(['result' => false, 'error' => 'Invalid name']);
    exit;
}

$vehicle->lastUpdate = Date('Y-m-d H:i:s');
$vehicle->lastUpdatedBy = $user->getUsername();
$vehicle->save(userResponsibleForOperation: $user->getUsername());

echo json_encode(['result' => true]);
