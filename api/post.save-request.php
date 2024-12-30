<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

require_once 'class.trip.php';
require_once 'class.event.php';
require_once 'class.guest.php';
require_once 'class.airport.php';
require_once 'class.airport-location.php';

switch ($json->type) {
  case 'airport-dropoff':
    addAirportDropoff($json);
    break;

  case 'airport-pickup':
    addAirportPickup($json);
    break;

  case 'point-to-point':
    addPointToPoint($json);
    break;

  case 'vehicle':
    addVehicleReservation($json);
    break;

  case 'event':
    addEvent($json);
    break;
}

echo json_encode([
  'result' => true, 
]);

function addAirportDropoff($json)
{
  $airport = new Airport();
  $airport->getAirportByIATA($json->airport);
  $turnAroundTime = 30;
  $summary = $json->airport.' Drop Off - '.$json->whom->name;

  $trip = new trip();
  $trip->requestorId = $json->requestorId;
  $trip->summary = $summary;
  $trip->startDate = Date('Y-m-d H:i:s', strtotime($json->datetime));
  $trip->endDate = Date('Y-m-d H:i:s', strtotime($json->datetime) + (($json->flight->travelTime *2) + $turnAroundTime) * 60);
  $trip->pickupDate = Date('Y-m-d H:i:s', strtotime($json->datetime));
  $trip->guests = $json->whom->name;

  if ($guest = Guest::getGuestByPhoneNumber(Guest::formattedPhoneNumber($json->whom->contactPhoneNumber))) {
    $trip->guestId = $guest->guestId;
  } else {
    $parts = explode(' ', $json->whom->name);
    $guest = new Guest(null);
    $guest->lastName = array_pop($parts);
    $guest->firstName = implode(' ', $parts);
    $guest->phoneNumber = Guest::formattedPhoneNumber($json->whom->contactPhoneNumber);
    $guest->save();
    $trip->guestId = $guest->guestId;
  }

  $trip->passengers = $json->whom->pax;
  $trip->airlineId = $json->flight->airlineId;
  $trip->flightNumber = $json->flight->flightNumber;
  $trip->ETD = Date('Y-m-d H:i:s', strtotime($json->flight->flightTime));
  $trip->doLocationId = AirportLocation::getAirportLocation($airport->getAirportId(), $json->flight->airlineId, 'Departure');
  $trip->generalNotes = $json->notes;
  $trip->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $trip->save();
}

function addAirportPickup($json)
{
  $airport = new Airport();
  $airport->getAirportByIATA($json->airport);
  $waitTimeAtAirport = 30;
  $summary = $json->airport.' Pick Up - '.$json->whom->name;
  $trip = new trip();
  $trip->requestorId = $json->requestorId;
  $trip->summary = $summary;
  $trip->startDate = Date('Y-m-d H:i:s', strtotime($json->flight->flightTime) - ($json->flight->travelTime *60));
  $trip->pickupDate = Date('Y-m-d H:i:s', strtotime($json->flight->flightTime));
  $trip->endDate = Date('Y-m-d H:i:s', strtotime($json->flight->flightTime) + ($json->flight->travelTime + $waitTimeAtAirport) * 60);
  $trip->guests = $json->whom->name;

  if ($guest = Guest::getGuestByPhoneNumber(Guest::formattedPhoneNumber($json->whom->contactPhoneNumber))) {
    $trip->guestId = $guest->guestId;
  } else {
    $parts = explode(' ', $json->whom->name);
    $guest = new Guest(null);
    $guest->lastName = array_pop($parts);
    $guest->firstName = implode(' ', $parts);
    $guest->phoneNumber = Guest::formattedPhoneNumber($json->whom->contactPhoneNumber);
    $guest->save();
    $trip->guestId = $guest->guestId;
  }

  $trip->passengers = $json->whom->pax;
  $trip->airlineId = $json->flight->airlineId;
  $trip->flightNumber = $json->flight->flightNumber;
  $trip->ETA = Date('Y-m-d H:i:s', strtotime($json->flight->flightTime));
  $trip->puLocationId = AirportLocation::getAirportLocation($airport->getAirportId(), $json->flight->airlineId, 'Arrival');
  $trip->generalNotes = $json->notes;
  $trip->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $trip->save();
}

function addPointToPoint($json)
{
  $summary = 'Transport - '.$json->whom->name;
  $trip = new trip();
  $trip->requestorId = $json->requestorId;
  $trip->summary = $summary;
  $roundTripDuration = 5*60; // 5 hours - just an arbitrary time. When we update the trip with actual locations we can adjust this.

  $trip->startDate = Date('Y-m-d H:i:s', strtotime($json->datetime));
  $trip->pickupDate = Date('Y-m-d H:i:s', strtotime($json->datetime));
  $trip->endDate = Date('Y-m-d H:i:s', strtotime($json->datetime) + $roundTripDuration * 60);
  $trip->guests = $json->whom->name;

  if ($guest = Guest::getGuestByPhoneNumber(Guest::formattedPhoneNumber($json->whom->contactPhoneNumber))) {
    $trip->guestId = $guest->guestId;
  } else {
    $parts = explode(' ', $json->whom->name);
    $guest = new Guest(null);
    $guest->lastName = array_pop($parts);
    $guest->firstName = implode(' ', $parts);
    $guest->phoneNumber = Guest::formattedPhoneNumber($json->whom->contactPhoneNumber);
    $guest->save();
    $trip->guestId = $guest->guestId;
  }

  $trip->passengers = $json->whom->pax;
  $trip->generalNotes = $json->notes;
  $trip->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $trip->save();
}

function addVehicleReservation($json)
{
  $name = 'Vehicle Reservation - '.$json->whom->name;
  $event = new Event();
  $event->name = $name;
  $event->requestorId = $json->requestorId;
  $event->startDate = Date('Y-m-d H:i:s', strtotime($json->startDate));
  $event->endDate = Date('Y-m-d H:i:s', strtotime($json->endDate));
  $event->notes = $json->notes;
  $event->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $event->save();
}

function addEvent($json)
{
  $name = 'Event';
  $event = new Event();
  $event->name = $name;
  $event->requestorId = $json->requestorId;
  $event->startDate = Date('Y-m-d H:i:s', strtotime($json->startDate));
  $event->endDate = Date('Y-m-d H:i:s', strtotime($json->endDate));
  $event->notes = $json->detail;
  $event->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $event->save();
}