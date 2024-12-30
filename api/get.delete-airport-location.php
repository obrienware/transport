<?php
header('Content-Type: application/json');
require_once 'class.airport-location.php';
$airportLocation = new AirportLocation($_REQUEST['id']);
$result = $airportLocation->delete();
die(json_encode(['result' => $result]));