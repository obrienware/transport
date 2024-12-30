<?php
header('Content-Type: application/json');
require_once 'class.airport-location.php';
$json = json_decode(file_get_contents("php://input"));

$airportLocation = new AirportLocation($json->id);
$airportLocation->airportId = $json->airportId ?: NULL;
$airportLocation->airlineId = $json->airlineId ?: NULL;
$airportLocation->locationId = $json->locationId ?: NULL;
$airportLocation->type = $json->type ?: NULL;

$result = $airportLocation->save();
echo json_encode(['result' => $result]);