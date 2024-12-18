<?php
header('Content-Type: application/json');
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.audit.php';
require_once 'class.vehicle.php';
$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
if (isset($json->mileage)) $vehicle->mileage = $json->mileage;
if (isset($json->locationId)) $vehicle->locationId = $json->locationId;
if (isset($json->hasCheckEngine)) $vehicle->hasCheckEngine = $json->hasCheckEngine;
if (isset($json->restock)) $vehicle->restock = $json->restock;
if (isset($json->cleanInterior)) $vehicle->cleanInterior = $json->cleanInterior;
if (isset($json->cleanExterior)) $vehicle->cleanExterior = $json->cleanExterior;
if (isset($json->fuelLevel)) $vehicle->fuelLevel = $json->fuelLevel;
$vehicle->lastUpdate = Date('Y-m-d H:i:s');
$vehicle->lastUpdatedBy = $_SESSION['user']->username;
$vehicle->save();

$before = $vehicle->getState();
$vehicle->getVehicle($vehicle->getId());
$after = $vehicle->getState();
$description = 'Updated vehicle condition/status: '.$vehicle->name;
Audit::log('update', 'vehicles', $description, $before, $after);

echo json_encode(['result' => true]);