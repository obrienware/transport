<?php
header('Content-Type: application/json');
// date_default_timezone_set('America/Denver');

require_once '../autoload.php';

use Transport\Trip;
use Transport\Event;

$vehicleId = !empty($_GET['id']) ? (int)$_GET['id'] : null;

// Let's check for trips first
$id = Trip::nextTripByVehicle($vehicleId);
if ($id) $trip = new Trip($id);

// Then check for events
$id = Event::nextEventByVehicle($vehicleId);
if ($id) $event = new Event($id);



if (isset($trip) && !(isset($event))) {
  // We only have an upcoming trip
  die(json_encode([
    'type' => 'trip',
    'name' => $trip->summary,
    'starts' => $trip->startDate
  ]));
}

if (isset($event) && !(isset($trip))) {
  // We only have an upcoming event
  die(json_encode([
    'type' => 'event',
    'name' => $event->name,
    'starts' => $event->startDate
  ]));
}

if (isset($trip) && isset($event)) {
  // We need to determine which is more recent
  if ($event->startDate < $trip->startDate) {
    die(json_encode([
      'type' => 'event',
      'name' => $event->name,
      'starts' => $event->startDate
    ]));  
  }
  die(json_encode([
    'type' => 'trip',
    'name' => $trip->summary,
    'starts' => $trip->startDate
  ]));  
}

echo json_encode(['starts' => null]);