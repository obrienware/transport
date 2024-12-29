<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

require_once 'class.trip.php';
require_once 'class.event.php';
require_once 'class.guest.php';

switch ($json->type) {
  case 'airport-dropoff':
    addAirportDropoff($json);
    break;

  case 'airport-pickup':
    addAirportPickup($json);
    break;

  case 'point-to-point':
    break;

  case 'vehicle':
    break;

  case 'event':
    break;
}

echo json_encode([
  'result' => true, 
]);

function addAirportDropoff($json)
{
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
  // $trip->doLocationId // We should be able to get the airport location (based on the airport and airline)
  $trip->generalNotes = $json->notes;
  $trip->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $trip->save();
}

function addAirportPickup($json)
{
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
  // $trip->puLocationId // We should be able to get the airport location (based on the airport and airline)
  $trip->generalNotes = $json->notes;
  $trip->originalRequest = json_encode($json, JSON_PRETTY_PRINT);
  $trip->save();
}