<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.event.php';
$event = new Event($json->eventId);
$event->cancel();
die(json_encode(['result' => true]));