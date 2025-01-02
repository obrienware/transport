<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.airport-location.php';
$json = json_decode(file_get_contents("php://input"));

$airportLocation = new AirportLocation($json->id);
$airportLocation->airportId = $json->airportId ?: NULL;
$airportLocation->airlineId = $json->airlineId ?: NULL;
$airportLocation->locationId = $json->locationId ?: NULL;
$airportLocation->type = $json->type ?: NULL;

if ($airportLocation->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $airportLocation->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));