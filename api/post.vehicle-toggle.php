<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);

$newState = rotateState($json->state);
switch ($json->name) {
  case 'restock':
    $vehicle->restock = $newState;
    break;
  case 'cleanInterior':
    $vehicle->cleanInterior = $newState;
    break;
  case 'cleanExterior':
    $vehicle->cleanExterior = $newState;
    break;
  case 'hasCheckEngine':
    $vehicle->hasCheckEngine = $newState;
    break;
  default:
    echo json_encode(['result' => false, 'error' => 'Invalid name']);
    exit;
}

$vehicle->lastUpdate = Date('Y-m-d H:i:s');
$vehicle->lastUpdatedBy = $user->getUsername();
$vehicle->save(userResponsibleForOperation: $user->getUsername());

echo json_encode(['result' => true, 'state' => $newState]);

function rotateState($state) {
  if ($state === true) return false;
  if ($state === false) return null;
  if ($state === null) return true;
}