<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
$vehicle->locationId = parseValue($json->locationId);
$vehicle->mileage = parseValue($json->mileage);
$vehicle->fuelLevel = hasValue($json->fuelLevel) ? (int) $json->fuelLevel : NULL;
$vehicle->hasCheckEngine = parseValue($json->checkengineOn);
$vehicle->cleanInterior = parseValue($json->isCleanInside);
$vehicle->cleanExterior = parseValue($json->isCleanOutside);
$vehicle->restock = parseValue($json->needsRestocking);
$vehicle->lastUpdate = 'now';
$vehicle->lastUpdatedBy = $_SESSION['user']->username;

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : NULL;
}

if ($vehicle->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $vehicle->getId()]));
}
exit(json_encode([
  'result' => false,
  'message' => $vehicle->getLastError()
]));