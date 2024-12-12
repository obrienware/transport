<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.location.php';
$json = json_decode(file_get_contents("php://input"));

$location = new Location($json->id);
$previousName = $location->name;

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
if ($json->id) {
  $before = $location->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed location: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added location: '.$json->name;
}
$location->getLocation($id);
$after = $location->getState();
Audit::log($action, 'locations', $description, $before, $after);

echo json_encode(['result' => $result]);
