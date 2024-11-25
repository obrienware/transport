<?php
header('Content-Type: application/json');
require_once 'class.airport.php';
require_once 'class.location.php';
require_once 'class.trip.php';
$json = json_decode(file_get_contents("php://input"));

$trip = new Trip($json->id);
$trip->requestorId = $json->requestorId ?: NULL;
$trip->summary = $json->summary ?: NULL;
$trip->startDate = $json->startDate ?: NULL;
$trip->endDate = $json->endDate ?: NULL;
$trip->guestId = $json->guestId ?: NULL;
$trip->passengers = $json->passengers ?: NULL;
$trip->puLocationId = $json->puLocationId ?: NULL;
$trip->doLocationId = $json->doLocationId ?: NULL;
$trip->driverId = $json->driverId ?: NULL;
$trip->vehicleId = $json->vehicleId ?: NULL;
$trip->airlineId = $json->airlineId ?: NULL;
$trip->flightNumber = $json->flightNumber ?: NULL;
$trip->vehiclePUOptions = $json->vehiclePUOptions ?: NULL;
$trip->vehicleDOOptions = $json->vehicleDOOptions ?: NULL;
$trip->ETA = $json->ETA ?: NULL;
$trip->ETD = $json->ETD ?: NULL;
$trip->guestNotes = $json->guestNotes ?: NULL;
$trip->driverNotes = $json->driverNotes ?: NULL;
$trip->generalNotes = $json->generalNotes ?: NULL;
$result = $trip->save();

echo json_encode([
  'result' => $result
]);
ob_end_flush(); // No more output to the requestor


$tripId = $json->id ?: $result['result'];

// Let's generate some waypoints!
require_once 'class.vehicle.php';
require_once 'class.waypoint.php';

$wp = new Waypoints($tripId);

// First we need the vehicle's usual staging location - assuming that where the vehicle will be / will be returned to
$vehicle = new Vehicle($trip->vehicleId);

if ($trip->vehiclePUOptions == 'pick up from staging') {
  $wp->add('Vehicle pick up', $vehicle->stagingLocationId);
}

if ($trip->ETA) {
  // This means we're picking up from an airport and so the next waypoint will be the airport staging area
  $location = new Location($trip->puLocationId);
  $iata = $location->IATA;
  $airport = new Airport();
  $airport->getAirportByIATA($iata);
  $wp->add('Airport staging area', $airport->stagingLocationId, $trip->ETA);
}

$wp->add('Guest pick up', $trip->puLocationId);

if ($trip->ETD) {
  $location = new Location($trip->doLocationId);
  $iata = $location->IATA;
  $airport = new Airport();
  $airport->getAirportByIATA($iata);
  $targetDateTime = Date('Y-m-d H:i:s', strtotime($trip->ETD) - ($airport->leadTime * 60));
  $wp->add('Guest/Group drop off', $trip->doLocationId, $targetDateTime);
} else {
  $wp->add('Guest/Group drop off', $trip->doLocationId);
}

if ($trip->vehicleDOOptions == 'return to staging') {
  $wp->add('Vehicle drop off', $vehicle->stagingLocationId);
}

$wp->save(); // This will only actually generate waypoints in the database if none already exist.
