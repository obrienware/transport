<?php

declare(strict_types=1);

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
use Transport\VehicleReservation;
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);


$changes = [];
$driversToNotify = [];

$trip = new Trip($input->getInt('id'));

if ($trip->getId() && $trip->isConfirmed() && $trip->endDate > Date('Y-m-d H:i:s'))
{
  // We are only interested in tracking changes to existing trips AND if the trip is confirmed
  if ($trip->requestorId != $input->getInt('requestorId'))
  {
    if (!$trip->requestorId)
    {
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    }
    else
    {
      $requestor = new User($trip->requestorId);
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($trip->summary != $input->getString('summary')) $changes[] = "The trip summary was changed from \"{$trip->summary}\" to \"{$input->getString('summary')}\"";
  if ($trip->pickupDate != $input->getString('pickupDate')) $changes[] = "The pick up date/time was changed from \"{$trip->pickupDate}\" to \"{$input->getString('pickupDate')}\"";
  if ($trip->guests != $input->getString('guests')) $changes[] = "Guest(s) have changed from \"{$trip->guests}\" to \"{$input->getString('guests')}\"";
  if ($trip->guestId != $input->getInt('guestId'))
  {
    if (!$trip->guestId)
    {
      $newGuest = new User($input->getInt('guestId'));
      $changes[] = "Contact person was assigned: {$newGuest->getName()} {$newGuest->phoneNumber}";
    }
    else
    {
      $guest = new User($trip->guestId);
      $newGuest = new User($input->getInt('guestId'));
      $changes[] = "Contact person changed from \"{$guest->getName()}\" to \"{$newGuest->getName()} {$newGuest->phoneNumber}\"";
    }
  }
  if ($trip->passengers != $input->getInt('passengers')) $changes[] = "The number of passengers was changed from \"{$trip->passengers}\" to \"{$input->getInt('passengers')}\"";
  if ($trip->puLocationId != $input->getInt('puLocationId'))
  {
    if (!$trip->puLocationId)
    {
      $newLocation = new Location($input->getInt('puLocationId'));
      $changes[] = "Pick up location was assigned: {$newLocation->name}";
    }
    else
    {
      $location = new Location($trip->puLocationId);
      $newLocation = new Location($input->getInt('puLocationId'));
      $changes[] = "The pick up location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($trip->doLocationId != $input->getInt('doLocationId'))
  {
    if (!$trip->doLocationId)
    {
      $newLocation = new Location($input->getInt('doLocationId'));
      $changes[] = "Drop off location was assigned: {$newLocation->name}";
    }
    else
    {
      $location = new Location($trip->doLocationId);
      $newLocation = new Location($input->getInt('doLocationId'));
      $changes[] = "The drop off location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($trip->driverId != $input->getInt('driverId'))
  {
    if (!$trip->driverId)
    {
      $newDriver = new User($input->getInt('driverId'));
      $changes[] = "Driver was assigned: {$newDriver->getName()}";
      $driversToNotify[] = $newDriver;
    }
    else
    {
      $driver = new User($trip->driverId);
      $newDriver = new User($input->getInt('driverId'));
      $changes[] = "The driver was changed from \"{$driver->getName()}\" to \"{$newDriver->getName()}\"";
      $driversToNotify[] = $driver;
      $driversToNotify[] = $newDriver;
    }
  }
  if ($trip->vehicleId != $input->getInt('vehicleId'))
  {
    if (!$trip->vehicleId)
    {
      $newVehicle = new Vehicle($input->getInt('vehicleId'));
      $changes[] = "Vehicle was assigned: {$newVehicle->name}";
    }
    else
    {
      $vehicle = new Vehicle($trip->vehicleId);
      $newVehicle = new Vehicle($input->getInt('vehicleId'));
      $changes[] = "The vehicle was changed from \"{$vehicle->name}\" to \"{$newVehicle->name}\"";
    }
  }
  if ($trip->airlineId != $input->getInt('airlineId'))
  {
    if (!$trip->airlineId)
    {
      $newAirline = new Airline($input->getInt('airlineId'));
      $changes[] = "Airline was assigned: {$newAirline->name}";
    }
    else
    {
      $airline = new Airline($trip->airlineId);
      $newAirline = new Airline($input->getInt('airlineId'));
      $changes[] = "The airline was changed from \"{$airline->name}\" to \"{$newAirline->name}\"";
    }
  }
  if ($trip->flightNumber != $input->getString('flightNumber')) $changes[] = "The flight number was changed from \"{$trip->flightNumber}\" to \"{$input->getString('flightNumber')}\"";
  if ($trip->vehiclePUOptions != $input->getString('vehiclePUOptions')) $changes[] = "The vehicle pick up option was changed from \"{$trip->vehiclePUOptions}\" to \"{$input->getString('vehiclePUOptions')}\"";
  if ($trip->vehicleDOOptions != $input->getString('vehicleDOOptions')) $changes[] = "The vehicle drop off option was changed from \"{$trip->vehicleDOOptions}\" to \"{$input->getString('vehicleDOOptions')}\"";
  if ($trip->ETA != $input->getString('ETA')) $changes[] = "The estimated time of arrival was changed from \"{$trip->ETA}\" to \"{$input->getString('ETA')}\"";
  if ($trip->ETD != $input->getString('ETD')) $changes[] = "The estimated time of departure was changed from \"{$trip->ETD}\" to \"{$input->getString('ETD')}\"";
  if ($trip->guestNotes != $input->getString('guestNotes')) $changes[] = "Guest notes changed: \n{$input->getString('guestNotes')}";
  if ($trip->driverNotes != $input->getString('driverNotes')) $changes[] = "Driver notes changed: \n{$input->getString('driverNotes')}";
  if ($trip->generalNotes != $input->getString('generalNotes')) $changes[] = "General notes changed: \n{$input->getString('generalNotes')}";
}


$trip->requestorId = $input->getInt('requestorId');
$trip->summary = $input->getString('summary');
$trip->startDate = $input->getString('startDate');
$trip->pickupDate = $input->getString('pickupDate');
$trip->endDate = $input->getString('endDate');
$trip->guests = $input->getString('guests');
$trip->guestId = $input->getInt('guestId');
$trip->passengers = $input->getInt('passengers');
$trip->puLocationId = $input->getInt('puLocationId');
$trip->doLocationId = $input->getInt('doLocationId');
$trip->driverId = $input->getInt('driverId');
$trip->vehicleId = $input->getInt('vehicleId');
$trip->airlineId = $input->getInt('airlineId');
$trip->flightNumber = $input->getString('flightNumber');
$trip->vehiclePUOptions = $input->getString('vehiclePUOptions');
$trip->vehicleDOOptions = $input->getString('vehicleDOOptions');
$trip->ETA = $input->getString('ETA');
$trip->ETD = $input->getString('ETD');
$trip->guestNotes = $input->getString('guestNotes');
$trip->driverNotes = $input->getString('driverNotes');
$trip->generalNotes = $input->getString('generalNotes');

if ($trip->ETA)
{
  $location = new Location($trip->puLocationId);
  $trip->IATA = $location->IATA;
}
if ($trip->ETD)
{
  $location = new Location($trip->doLocationId);
  $trip->IATA = $location->IATA;
}


// Check for and update any linked vehicle reservations
if ($trip->getId())
{
  if ($reservation = VehicleReservation::getReservationByTripId($trip->getId()))
  {
    if ($trip->getId() == $reservation->startTripId)
    {
      $reservation->startDateTime = $trip->endDate;
    }
    else
    {
      $reservation->endDateTime = $trip->startDate;
    }
    $reservation->save(userResponsibleForOperation: $user->getUsername());
  }
}


if ($trip->save(userResponsibleForOperation: $user->getUsername()))
{
  $result = $trip->getId();
  if ($changes) notifyParticipants($trip, $changes, $driversToNotify);

  if ($input->getString('vehiclePUOptions') === 'guest will have vehicle')
  {
    // There may be a open-ended vehicle reservation for this guest. If there is, we need to link this trip to it.
    if ($reservation = VehicleReservation::getOpenEndedReservation($trip->guestId))
    {
      $reservation->endTripId = $trip->getId();
      $reservation->endDateTime = $trip->startDate;
      $reservation->save(userResponsibleForOperation: $user->getUsername());
    }
    die(json_encode(['result' => $result]));
  }

  if ($input->getBool('createVehicleReservation'))
  {
    $reservation = new VehicleReservation();
    $reservation->guestId = $trip->guestId;
    $reservation->vehicleId = $trip->vehicleId;
    $reservation->startTripId = $trip->getId();
    $reservation->startDateTime = $trip->endDate;
    $reservation->save(userResponsibleForOperation: $user->getUsername());
  }
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false, 'error' => $trip->getLastError()]));


function notifyParticipants(Trip $trip, array $changes, array $driversToNotify): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $changesString = '- ' . implode("\n\n- ", $changes);

  // Email to the requestor (include the guest sheet to pass along)
  if ($trip->requestor)
  {
    // Generate the guest sheet
    include '../inc.trip-guest-sheet.php';
    $filename2 = sys_get_temp_dir() . '/' . $trip->getId() . '-trip-guest-sheet.pdf';
    $pdf->output('F', $filename2);

    $template = new Template(EmailTemplates::get('Email Requestor Trip Change'));
    $templateData = [
      'name' => $trip->requestor->firstName,
      'tripSummary' => $trip->summary,
      'changes' => $changesString,
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor')
    {
      if ($trip->requestor) $email->addRecipient($trip->requestor->emailAddress);
    }
    else
    {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding trip: ' . $trip->summary);
    $email->setContent($template->render($templateData));
    $email->addAttachment($filename2);
    $email->sendText();
    unlink($filename2);
  }


  // Email the driver(s)
  if (count($driversToNotify) > 0)
  {

    // Generate the driver sheet
    include '../inc.trip-driver-sheet.php';
    $filename1 = sys_get_temp_dir() . '/' . $trip->getId() . '-trip-driver-sheet.pdf';
    $pdf->output('F', $filename1);

    // Generate ics file
    include '../inc.trip-ics.php';


    $template = new Template(EmailTemplates::get('Email Driver Trip Change'));
    foreach ($driversToNotify as $driver)
    {
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
      $email->setSubject('Confirmation of changes regarding trip: ' . $trip->summary);
      $email->setContent($template->render($templateData));
      $email->addAttachment($filename1);
      $ical = $ics->to_string();
      $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
      $email->sendText();
    }

    unlink($filename1);
  }
}

function hasValue($value)
{
  return isset($value) && $value !== '';
}

function parseValue($value)
{
  return hasValue($value) ? $value : NULL;
}

function parseValueInt($value)
{
  return hasValue($value) ? (int)$value : NULL;
}
