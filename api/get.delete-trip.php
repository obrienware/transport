<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);
$result = $trip->delete();
die(json_encode(['result' => $result]));