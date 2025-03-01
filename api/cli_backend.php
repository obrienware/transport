<?php

declare(strict_types=1);

require_once '../autoload.php';

// fix use Transport\Database;
use Transport\Flight;

header('Content-Type: text/plain');

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$command = $input['command'] ?? '';

if (!$command) {
  echo "Error: No command provided.";
  exit;
}

$flight = new Flight();

$parts = explode(" ", $command);
$cmd = strtolower($parts[0]);

switch ($cmd) {
  case "lookup-flight":
    if (count($parts) < 3) {
      echo htmlentities("Usage: lookup-flight <flight_number> <iata> <date>");
      exit;
    }
    $flightIata = $parts[1];
    $iata = $parts[2];
    $date = $parts[3];

    if ($result = $flight->getFlightStatus($flightIata, null, $iata, $date)) {
      foreach ($result as $flight) {
        echo "Flight: " . $flight->flight_number . "\n";
        echo "Departure: " . $flight->airport_origin_iata . " at " . $flight->scheduled_departure . "\n";
        echo "Arrival: " . $flight->airport_destination_iata . " at " . $flight->scheduled_arrival . "\n";
        echo "\n";
      }
    } else {
      echo "No flight schedule data found for this flight.";
    }

    break;

  case "clear-cache":
    $db->query("DELETE FROM flight_schedules", []);
    echo "Flight cache cleared!";
    break;

  default:
    echo "Unknown command: $command";
    break;
}
