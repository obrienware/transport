<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new Data();
$json = json_decode(file_get_contents("php://input"));

if ($json->tripId && $json->message) {
  $sql = "
    INSERT INTO trip_messages (datetimestamp, trip_id, user_id, message)
    VALUES (NOW(), :trip_id, :user_id, :message)
  ";
  $data = [
    'trip_id' => $json->tripId,
    'user_id' => $_SESSION['user']->id,
    'message' => $json->message
  ];
  $result = $db->query($sql, $data);
}

if ($json->eventId && $json->message) {
  $sql = "
    INSERT INTO event_messages (datetimestamp, event_id, user_id, message)
    VALUES (NOW(), :event_id, :user_id, :message)
  ";
  $data = [
    'event_id' => $json->eventId,
    'user_id' => $_SESSION['user']->id,
    'message' => $json->message
  ];
  $result = $db->query($sql, $data);
}

echo json_encode(['result' => $result]);