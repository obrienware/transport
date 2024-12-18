<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
require_once 'class.event.php';
require_once 'class.data.php';
$db = new data();
$vehicleId = $_REQUEST['id'];

// Let's check for trips first
$sql = "
  SELECT id FROM trips 
  WHERE  NOW() < start_date AND vehicle_id = :vehicle_id
  ORDER BY start_date
  LIMIT 1
";
$data = ['vehicle_id' => $vehicleId];
if ($id = $db->get_var($sql, $data)) {
  $trip = new Trip($id);
}

// Then check for events
$sql = "
  SELECT id FROM events 
  WHERE  NOW() < start_date AND FIND_IN_SET(:vehicle_id, vehicle_ids)
  ORDER BY start_date
  LIMIT 1
";
$data = ['vehicle_id' => $vehicleId];
if ($id = $db->get_var($sql, $data)) {
  $event = new Event($id);
}

if (isset($trip) && !(isset($event))) {
  // We only have an upcoming trip
  die(json_encode([
    'type' => 'trip',
    'name' => $trip->summary,
    'starts' => $trip->startDate
  ]));
} elseif (isset($event) && !(isset($trip))) {
  // We only have an upcoming event
  die(json_encode([
    'type' => 'event',
    'name' => $event->name,
    'starts' => $event->startDate
  ]));
} elseif (isset($trip) && isset($event)) {
  // We need to determine which is more recent
  if ($event->startDate < $trip->startDate) {
    die(json_encode([
      'type' => 'event',
      'name' => $event->name,
      'starts' => $event->startDate
    ]));  
  } else {
    die(json_encode([
      'type' => 'trip',
      'name' => $trip->summary,
      'starts' => $trip->startDate
    ]));  
  }
}
echo json_encode(['starts' => null]);