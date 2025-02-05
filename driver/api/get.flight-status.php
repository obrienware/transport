<?php
header('Content-Type: application/json');

require_once '../../autoload.php';

use Transport\Flight;

$flight = $_GET['flight'];
$type = $_GET['type'];
$iata = $_GET['iata'];
$date = $_GET['date'] ?? NULL;

$result = Flight::getFlightStatus($flight, $type, $iata, $date);
echo json_encode($result);