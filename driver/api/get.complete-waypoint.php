<?php
header('Content-Type: application/json');

require_once 'class.data.php';
$db = new data();
$sql = "UPDATE trip_waypoints SET reached = NOW() WHERE trip_id = :trip_id AND seq = :seq";
$data = [
  'trip_id' => $_REQUEST['tripId'],
  'seq' => $_REQUEST['seq']
];
$result = $db->query($sql, $data);

// Check if we have completed all the waypoints
$sql = "SELECT COUNT(*) FROM trip_waypoints WHERE trip_id = :trip_id AND reached IS NULL";
$data = ['trip_id' => $_REQUEST['tripId']];
$count = $db->get_var($sql, $data);
if ($count <= 0) {
  // Mark the trip complete also
  $sql = "UPDATE trips SET completed = NOW() WHERE id = :trip_id";
  $db->query($sql, $data);
}

echo json_encode([
  'result' => $result,
  'complete' => ($count <= 0)
]);


// TODO: When we reach the guest pickup location, we should send the guest a message. If the location has a message template, we should use that, otherwise a generic message to the effect of:
// Your driver (Richard) has arrived at your pick up location in a white Chrysler minivan, and is available to help with luggage if needed.