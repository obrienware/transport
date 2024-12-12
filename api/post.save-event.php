<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.event.php';
$json = json_decode(file_get_contents("php://input"));

$event = new Event($json->eventId);
$previousName = $event->name;

$event->name = $json->name ?: NULL;
$event->requestorId = $json->requestorId ?: NULL;
$event->locationId = $json->locationId ?: NULL;
$event->startDate = $json->startDate ?: NULL;
$event->endDate = $json->endDate ?: NULL;
$event->drivers = $json->drivers ?: [];
$event->vehicles = $json->vehicles ?: [];
$event->notes = $json->notes ?: NULL;

$result = $event->save();
if ($json->id) {
  $before = $event->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed event: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added event: '.$json->name;
}
$event->getEvent($id);
$after = $event->getState();
Audit::log($action, 'events', $description, $before, $after);


echo json_encode(['result' => $result]);
