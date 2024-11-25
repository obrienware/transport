<?php
header('Content-Type: application/json');
require_once 'class.location.php';
$json = json_decode(file_get_contents("php://input"));
$location = new Location($json->id);

$location->name = $json->name ?: NULL;
$location->shortName = $json->shortName ?: NULL;
$location->description = $json->description ?: NULL;
$location->type = $json->type ?: NULL;
$location->IATA = $json->IATA ?: NULL;
$location->mapAddress = $json->mapAddress ?: NULL;
$location->lat = $json->lat ?: NULL;
$location->lon = $json->lon ?: NULL;
$location->placeId = $json->placeId ?: NULL;
$location->meta = json_encode($json->meta);
$result = $location->save();

echo json_encode(['result' => $result]);
