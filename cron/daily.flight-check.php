<?php
// Our objective here is to get some kind of flight confirmation
// for flights coming up in the next 7 days (excluding today) - Today's is handled separately

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Flight;

$updatedFlightNumbers = [];

if ($flights = Flight::upcomingFlights())
{
  foreach ($flights as $flight)
  {
    if (!$flight->flight_number) continue; // exclude flights we don't have flight numbers for
    $updatedFlightNumbers[] = [
      $flight->flight_number => Flight::updateFlight($flight->flight_number)
    ];
  }
}

echo json_encode(['updatedFlightNumbers' => $updatedFlightNumbers]);