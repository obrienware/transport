<?php
header('Content-Type: application/json');
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
$vehicle->save();

echo json_encode(['result' => true]);