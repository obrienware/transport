<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.trip.php';
$trip = new Trip($json->tripId);
$trip->cancel();
die(json_encode(['result' => true]));