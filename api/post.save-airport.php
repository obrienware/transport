<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.airport.php';
$json = json_decode(file_get_contents("php://input"));

$airport = new Airport($json->id);
$previousName = $airport->name;

$airport->IATA = $json->iata ?: NULL;
$airport->name = $json->name ?: NULL;
$airport->stagingLocationId = $json->stagingLocationId ?: NULL;
$airport->leadTime = $json->leadTime ?: NULL;
$airport->travelTime = $json->travelTime ?: NULL;
$airport->arrivalInstructions = $json->arrivalInstructions ?: NULL;
$airport->arrivalInstructionsGroup = $json->arrivalInstructionsGroup ?: NULL;

$before = $airport->getState();
$result = $airport->save();
if ($json->id) {
  $action = 'modified';
  $description = 'Modified airport: '.$previousName;
} else {
  $action = 'added';
  $description = 'Added airport: '.$airport->name;
}
$after = $airport->getState();
Audit::log($action, 'airports', $description, $before, $after);
echo json_encode(['result' => $result]);