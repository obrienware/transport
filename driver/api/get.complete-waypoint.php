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
