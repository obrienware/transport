<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
$json = json_decode(file_get_contents("php://input"));

$trip = new Trip($json->id);
$result = $trip->finalize();

echo json_encode(['result' => $result]);