<?php
header('Content-Type: application/json');

date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.vehicle.php';
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
$vehicle->save();

echo json_encode(['result' => true]);
