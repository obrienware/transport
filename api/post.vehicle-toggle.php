<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$vehicle = new Vehicle($input->getInt('vehicleId'));
$newState = rotateState($input->getBool('state'));

switch ($input->getString('name'))
{
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

function rotateState($state)
{
  if ($state === true) return false;
  if ($state === false) return null;
  if ($state === null) return true;
}
