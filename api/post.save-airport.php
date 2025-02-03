<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airport;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$airport = new Airport($json->id);
$airport->IATA = parseValue($json->iata);
$airport->name = parseValue($json->name);
$airport->stagingLocationId = parseValue($json->stagingLocationId);
$airport->leadTime = parseValue($json->leadTime);
$airport->travelTime = parseValue($json->travelTime);
$airport->arrivalInstructions = parseValue($json->arrivalInstructions);
$airport->arrivalInstructionsGroup = parseValue($json->arrivalInstructionsGroup);

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : NULL;
}

if ($airport->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $airport->getId()]));
}
exit(json_encode(['result' => false, 'error' => $airport->getLastError()]));
