<?php
//TODO: This should have its own class
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$json = json_decode(file_get_contents("php://input"));

if ($json->tripId && $json->message) {
  $query = "
    INSERT INTO trip_messages (datetimestamp, trip_id, user_id, message)
    VALUES (NOW(), :trip_id, :user_id, :message)
  ";
  $params = [
    'trip_id' => $json->tripId,
    'user_id' => $_SESSION['user']->id,
    'message' => $json->message
  ];
  $result = $db->query($query, $params);
}

if ($json->eventId && $json->message) {
  $query = "
    INSERT INTO event_messages (datetimestamp, event_id, user_id, message)
    VALUES (NOW(), :event_id, :user_id, :message)
  ";
  $params = [
    'event_id' => $json->eventId,
    'user_id' => $_SESSION['user']->id,
    'message' => $json->message
  ];
  $result = $db->query($query, $params);
}

echo json_encode(['result' => $result]);