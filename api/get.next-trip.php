<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Event, Trip, Vehicle };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');
$vehicleId = $id === false ? null : $id;

$vehicle = new Vehicle($vehicleId);
if (!$vehicle->getId()) {
  exit(json_encode([
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
  exit(json_encode([
    'type' => 'trip',
    'name' => $trip->summary,
    'starts' => $trip->startDate
  ]));
}

if (isset($event) && !(isset($trip))) {
  // We only have an upcoming event
  exit(json_encode([
    'type' => 'event',
    'name' => $event->name,
    'starts' => $event->startDate
  ]));
}

if (isset($trip) && isset($event)) {
  // We need to determine which is more recent
  if ($event->startDate < $trip->startDate) {
    exit(json_encode([
      'type' => 'event',
      'name' => $event->name,
      'starts' => $event->startDate
    ]));  
  }
  exit(json_encode([
    'type' => 'trip',
    'name' => $trip->summary,
    'starts' => $trip->startDate
  ]));  
}

echo json_encode(['starts' => null]);