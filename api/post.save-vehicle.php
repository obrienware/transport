<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$vehicle = new Vehicle($json->vehicleId);
$vehicle->color = parseValue($json->color);
$vehicle->name = parseValue($json->name);
$vehicle->description = parseValue($json->description);
$vehicle->licensePlate = parseValue($json->licensePlate);
$vehicle->passengers = parseValue($json->passengers);
$vehicle->requireCDL = parseValue($json->requireCDL);

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : NULL;
}

if ($vehicle->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $vehicle->getId()]));
}
exit(json_encode(['result' => false, 'error' => $vehicle->getLastError()]));