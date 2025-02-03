<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Location;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$location = new Location($json->id);
$location->name = parseValue($json->name);
$location->shortName = parseValue($json->shortName);
$location->description = parseValue($json->description);
$location->type = parseValue($json->type);
$location->IATA = parseValue($json->IATA);
$location->mapAddress = parseValue($json->mapAddress);
$location->lat = parseValue($json->lat);
$location->lon = parseValue($json->lon);
$location->placeId = parseValue($json->placeId);
$location->meta = json_encode($json->meta);

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : NULL;
}

if ($location->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $location->getId()]));
}
exit(json_encode(['result' => false, 'error' => $location->getLastError()]));