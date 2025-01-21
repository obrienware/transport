<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\AirportLocation;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$airportLocation = new AirportLocation($json->id);
$airportLocation->airportId = parseValue($json->airportId);
$airportLocation->airlineId = parseValue($json->airlineId);
$airportLocation->locationId = parseValue($json->locationId);
$airportLocation->type = parseValue($json->type);

function hasValue($value) {
    return isset($value) && $value !== '';
}

function parseValue($value) {
    return hasValue($value) ? $value : NULL;
}

if ($airportLocation->save(userResponsibleForOperation: $user->getUsername())) {
    $result = $airportLocation->getId();
    die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));