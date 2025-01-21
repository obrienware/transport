<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
if ($json->locationId) $vehicle->locationId = $json->locationId;
if ($json->mileage) $vehicle->mileage = $json->mileage;
if ($json->fuelLevel) $vehicle->fuelLevel = $json->fuelLevel;
if (isset($json->checkengineOn)) $vehicle->hasCheckEngine = ($json->checkengineOn) ? 1 : 0;
if (isset($json->isCleanInside)) $vehicle->cleanInterior = ($json->isCleanInside) ? 1 : 0;
if (isset($json->isCleanOutside)) $vehicle->cleanExterior = ($json->isCleanOutside) ? 1 : 0;
if (isset($json->needsRestocking)) $vehicle->restock = ($json->needsRestocking) ? 1 : 0;
$vehicle->lastUpdate = Date('Y-m-d H:i:s');
$vehicle->lastUpdatedBy = $_SESSION['user']->username;

if ($vehicle->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $vehicle->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));