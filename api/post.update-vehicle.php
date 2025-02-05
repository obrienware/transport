<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Vehicle };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$vehicle = new Vehicle($input->getInt('id'));
$vehicle->locationId = $input->getInt('locationId');
$vehicle->mileage = $input->getInt('mileage');
$vehicle->fuelLevel = $input->getInt('fuelLevel');
$vehicle->hasCheckEngine = $input->getBool('checkengineOn');
$vehicle->cleanInterior = $input->getBool('isCleanInside');
$vehicle->cleanExterior = $input->getBool('isCleanOutside');
$vehicle->restock = $input->getBool('needsRestocking');
$vehicle->lastUpdate = 'now';
$vehicle->lastUpdatedBy = $_SESSION['user']->username;

if ($vehicle->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $vehicle->getId()]));
}
exit(json_encode([
  'result' => false,
  'message' => $vehicle->getLastError()
]));
