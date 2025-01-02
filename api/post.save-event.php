<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.event.php';
$json = json_decode(file_get_contents("php://input"));

$event = new Event($json->eventId);
$event->name = $json->name ?: NULL;
$event->requestorId = $json->requestorId ?: NULL;
$event->locationId = $json->locationId ?: NULL;
$event->startDate = $json->startDate ?: NULL;
$event->endDate = $json->endDate ?: NULL;
$event->drivers = $json->drivers ?: [];
$event->vehicles = $json->vehicles ?: [];
$event->notes = $json->notes ?: NULL;

if ($event->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $event->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
