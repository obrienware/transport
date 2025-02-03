<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Event;
use Transport\Trip;
use Transport\Vehicle;

$vehicleId = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$vehicle = new Vehicle($vehicleId);
if (!$vehicle->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Vehicle not found'
  ]));
}

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