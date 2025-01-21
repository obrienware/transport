<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Location;
use Transport\Template;
use Transport\Trip;
use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$changes = [];
$driversToNotify = [];

$trip = new Trip($json->id);

if ($trip->getId() && $trip->isConfirmed() && $trip->endDate > Date('Y-m-d H:i:s')) {
  // We are only interested in tracking changes to existing trips AND if the trip is confirmed
  if ($trip->requestorId != $json->requestorId) {
    if (!$trip->requestorId) {
      $newRequestor = new User($json->requestorId);
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    } else {
      $requestor = new User($trip->requestorId);
      $newRequestor = new User($json->requestorId);
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($trip->summary != $json->summary) $changes[] = "The trip summary was changed from \"{$trip->summary}\" to \"{$json->summary}\"";
  if ($trip->pickupDate != $json->pickupDate) $changes[] = "The pick up date/time was changed from \"{$trip->pickupDate}\" to \"{$json->pickupDate}\"";
  if ($trip->guests != $json->guests) $changes[] = "Guest(s) have changed from \"{$trip->guests}\" to \"{$json->guests}\"";
  if ($trip->guestId != $json->guestId) {
    if (!$trip->guestId) {
      $newGuest = new User($json->guestId);
      $changes[] = "Contact person was assigned: {$newGuest->getName()} {$newGuest->phoneNumber}";
    } else {
      $guest = new User($trip->guestId);
      $newGuest = new User($json->guestId);
      $changes[] = "Contact person changed from \"{$guest->getName()}\" to \"{$newGuest->getName()} {$newGuest->phoneNumber}\"";
    }
  }
  if ($trip->passengers != $json->passengers) $changes[] = "The number of passengers was changed from \"{$trip->passengers}\" to \"{$json->passengers}\"";
  if ($trip->puLocationId != $json->puLocationId) {
    if (!$trip->puLocationId) {
      $newLocation = new Location($json->puLocationId);
      $changes[] = "Pick up location was assigned: {$newLocation->name}";
    } else {
      $location = new Location($trip->puLocationId);
      $newLocation = new Location($json->puLocationId);
      $changes[] = "The pick up location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($trip->doLocationId != $json->doLocationId) {
    if (!$trip->doLocationId) {
      $newLocation = new Location($json->doLocationId);
      $changes[] = "Drop off location was assigned: {$newLocation->name}";
    } else {
      $location = new Location($trip->doLocationId);
      $newLocation = new Location($json->doLocationId);
      $changes[] = "The drop off location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($trip->driverId != $json->driverId) {
    if (!$trip->driverId) {
      $newDriver = new User($json->driverId);
      $changes[] = "Driver was assigned: {$newDriver->getName()}";
      $driversToNotify[] = $newDriver;
    } else {
      $driver = new User($trip->driverId);
      $newDriver = new User($json->driverId);
      $changes[] = "The driver was changed from \"{$driver->getName()}\" to \"{$newDriver->getName()}\"";
      $driversToNotify[] = $driver;
      $driversToNotify[] = $newDriver;
    }
  }
  if ($trip->vehicleId != $json->vehicleId) {
    if (!$trip->vehicleId) {
      $newVehicle = new Vehicle($json->vehicleId);
      $changes[] = "Vehicle was assigned: {$newVehicle->name}";
    } else {
      $vehicle = new Vehicle($trip->vehicleId);
      $newVehicle = new Vehicle($json->vehicleId);
      $changes[] = "The vehicle was changed from \"{$vehicle->name}\" to \"{$newVehicle->name}\"";
    }
  }
  if ($trip->airlineId != $json->airlineId) {
    if (!$trip->airlineId) {
      $newAirline = new Airline($json->airlineId);
      $changes[] = "Airline was assigned: {$newAirline->name}";
    } else {
      $airline = new Airline($trip->airlineId);
      $newAirline = new Airline($json->airlineId);
      $changes[] = "The airline was changed from \"{$airline->name}\" to \"{$newAirline->name}\"";
    }
  }
  if ($trip->flightNumber != $json->flightNumber) $changes[] = "The flight number was changed from \"{$trip->flightNumber}\" to \"{$json->flightNumber}\"";
  if ($trip->vehiclePUOptions != $json->vehiclePUOptions) $changes[] = "The vehicle pick up option was changed from \"{$trip->vehiclePUOptions}\" to \"{$json->vehiclePUOptions}\"";
  if ($trip->vehicleDOOptions != $json->vehicleDOOptions) $changes[] = "The vehicle drop off option was changed from \"{$trip->vehicleDOOptions}\" to \"{$json->vehicleDOOptions}\"";
  if ($trip->ETA != $json->ETA) $changes[] = "The estimated time of arrival was changed from \"{$trip->ETA}\" to \"{$json->ETA}\"";
  if ($trip->ETD != $json->ETD) $changes[] = "The estimated time of departure was changed from \"{$trip->ETD}\" to \"{$json->ETD}\"";
  if ($trip->guestNotes != $json->guestNotes) $changes[] = "Guest notes changed: \n{$json->guestNotes}";
  if ($trip->driverNotes != $json->driverNotes) $changes[] = "Driver notes changed: \n{$json->driverNotes}";
  if ($trip->generalNotes != $json->generalNotes) $changes[] = "General notes changed: \n{$json->generalNotes}";
}


$trip->requestorId = $json->requestorId ?: NULL;
$trip->summary = $json->summary ?: NULL;
$trip->startDate = $json->startDate ?: NULL;
$trip->pickupDate = $json->pickupDate ?: NULL;
$trip->endDate = $json->endDate ?: NULL;
$trip->guests = $json->guests ?: NULL;
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

if ($trip->ETA) {
  $location = new Location($trip->puLocationId);
  $trip->IATA = $location->IATA;
}
if ($trip->ETD) {
  $location = new Location($trip->doLocationId);
  $trip->IATA = $location->IATA;
}

if ($trip->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $trip->getId();
  if ($changes) notifyParticipants($trip, $changes);
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));


function notifyParticipants(Trip $trip, array $changes, array $driversToNotify): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $changesString = '- '.implode("\n\n- ", $changes);

  // Generate the driver sheet
  include '../inc.trip-driver-sheet.php';
  $filename1 = sys_get_temp_dir().'/'.$trip->getId().'-trip-driver-sheet.pdf';
  $pdf->output('F', $filename1);

  // Generate the guest sheet
  include '../inc.trip-guest-sheet.php';
  $filename2 = sys_get_temp_dir().'/'.$trip->getId().'-trip-guest-sheet.pdf';
  $pdf->output('F', $filename2);

  // Generate ics file
  include '../inc.trip-ics.php';


  // Email to the requestor (include the guest sheet to pass along)
  $template = new Template(EmailTemplates::get('Email Requestor Trip Change'));
  $templateData = [
    'name' => $trip->requestor->firstName,
    'tripSummary' => $trip->summary,
    'changes' => $changesString,
  ];

  $email = new Email();
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($trip->requestor) $email->addRecipient($trip->requestor->emailAddress);
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Confirmation of changes regarding trip: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->addAttachment($filename2);
  $email->sendText();


  // Email the driver(s)
  $template = new Template(EmailTemplates::get('Email Driver Trip Change'));
  foreach ($driversToNotify as $driver) {
    $templateData = [
      'name' => $driver->firstName,
      'tripDate' => Date('m/d/Y', strtotime($trip->pickupDate)),
      'tripSummary' => $trip->summary,
      'changes' => $changesString,
    ];
  
    $email = new Email();
    $email->addRecipient($driver->emailAddress, $driver->getName());
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding trip: '.$trip->summary);
    $email->setContent($template->render($templateData));
    $email->addAttachment($filename1);
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->sendText();  
  }

  unlink($filename1);
  unlink($filename2);
}

