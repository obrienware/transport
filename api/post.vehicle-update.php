<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Vehicle };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$vehicle = new Vehicle($input->getInt('vehicleId'));

switch ($input->getString('name'))
{
  case 'fuel':
    $vehicle->fuelLevel = $input->getInt('value');
    break;
  case 'mileage':
    $vehicle->mileage = $input->getInt('value');
    break;
  case 'location':
    $vehicle->locationId = $input->getInt('value');
    break;
  default:
    echo json_encode(['result' => false, 'error' => 'Invalid name']);
    exit;
}

$vehicle->lastUpdate = Date('Y-m-d H:i:s');
$vehicle->lastUpdatedBy = $user->getUsername();
$vehicle->save(userResponsibleForOperation: $user->getUsername());

echo json_encode(['result' => true]);
