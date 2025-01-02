<?php
header('Content-Type: application/json');
require_once 'class.airport.php';
$json = json_decode(file_get_contents("php://input"));

$airport = new Airport($json->id);
$airport->IATA = $json->iata ?: NULL;
$airport->name = $json->name ?: NULL;
$airport->stagingLocationId = $json->stagingLocationId ?: NULL;
$airport->leadTime = $json->leadTime ?: NULL;
$airport->travelTime = $json->travelTime ?: NULL;
$airport->arrivalInstructions = $json->arrivalInstructions ?: NULL;
$airport->arrivalInstructionsGroup = $json->arrivalInstructionsGroup ?: NULL;

if ($airport->save()) {
  $result = $airport->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
