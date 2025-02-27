<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Location, Flight };
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$flightIata = InputHandler::getString(INPUT_GET, 'flightIata');
$type = InputHandler::getString(INPUT_GET, 'type');
$date = InputHandler::getString(INPUT_GET, 'date');
$locationId = InputHandler::getInt(INPUT_GET, 'locationId');

$location = new Location($locationId);
$iata = $location->IATA;

$resp = Flight::getFlightStatus($flightIata, $type, $iata, $date);
echo json_encode($resp);