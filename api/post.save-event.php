<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.location.php';
require_once 'class.vehicle.php';
require_once 'class.event.php';
$json = json_decode(file_get_contents("php://input"));

$changes = [];

$event = new Event($json->eventId);

if ($event->getId() && $event->isConfirmed() && $event->endDate > Date('Y-m-d H:i:s')) {
  // We are only interested in tracking changes to existing events AND if the event is confirmed
  if ($event->name != $json->name) $changes[] = "The event name was changed from \"{$event->name}\" to \"{$json->name}\"";
  if ($event->requestorId != $json->requestorId) {
    if (!$event->requestorId) {
      $newRequestor = new User($json->requestorId);
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    } else {
      $requestor = new User($event->requestorId);
      $newRequestor = new User($json->requestorId);
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($event->locationId != $json->locationId) {
    if (!$event->locationId) {
      $newLocation = new Location($json->locationId);
      $changes[] = "Location was assigned to \"{$newLocation->name}\"";
    } else {
      $location = new Location($event->locationId);
      $newLocation = new Location($json->locationId);
      $changes[] = "The location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($event->startDate != $json->startDate) $changes[] = "The start date was changed from \"{$event->startDate}\" to \"{$json->startDate}\"";
  if ($event->endDate != $json->endDate) $changes[] = "The end date was changed from \"{$event->endDate}\" to \"{$json->endDate}\"";
  if ($event->drivers != $json->drivers) {
    $drivers = [];
    foreach ($event->drivers as $driverId) {
      if (!$driverId) continue;
      $driver = new User($driverId);
      $drivers[] = $driver->getName();
    }
    $newDrivers = [];
    foreach ($json->drivers as $driverId) {
      $driver = new User($driverId);
      $newDrivers[] = $driver->getName();
    }
    $changes[] = "The drivers were changed from \"".implode(', ', $drivers)."\" to \"".implode(', ', $newDrivers)."\"";
  }
  if ($event->vehicles != $json->vehicles) {
    $vehicles = [];
    foreach ($event->vehicles as $vehicleId) {
      $vehicle = new Vehicle($vehicleId);
      $vehicles[] = $vehicle->name;
    }
    $newVehicles = [];
    foreach ($json->vehicles as $vehicleId) {
      $vehicle = new Vehicle($vehicleId);
      $newVehicles[] = $vehicle->name;
    }
    $changes[] = "The vehicles were changed from \"".implode(', ', $vehicles)."\" to \"".implode(', ', $newVehicles)."\"";
  }
  if ($event->notes != $json->notes) {
    if (!$event->notes) {
      $changes[] = "Notes were added: \n".$json->notes;
    } else {
      $changes[] = "Notes were changed: \n".$json->notes;
    }
  }
}

$event->name = $json->name ?: NULL;
$event->requestorId = $json->requestorId ?: NULL;
$event->locationId = $json->locationId ?: NULL;
$event->startDate = $json->startDate ?: NULL;
$event->endDate = $json->endDate ?: NULL;
$event->drivers = $json->drivers ?: [];
$event->vehicles = $json->vehicles ?: [];
$event->notes = $json->notes ?: NULL;

if ($event->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $event->getId();
  if ($changes) notifyParticipants($event, $changes);
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));






// TODO: We want to send notifications to drivers who are no longer assigned to the event as well.

function notifyParticipants(Event $event, array $changes): void
{
  require_once 'class.ics.php';
  require_once 'class.config.php';
  require_once 'class.email.php';
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);

  // Generate ics file
  $ics = new ICS([
    'dtstart' => $event->startDate,
    'dtend' => $event->endDate,
    'description' => $event->notes,
    'summary' => $event->name,
  ]);
  if ($event->location) $ics->set('location', str_replace("\n", "\\n", $event->location->mapAddress));

  $changesString = '- '.implode("\n\n- ", $changes);
 
  
  // Email to the requestor
  $requestorName = $event->requestor->firstName;
  $email = new Email();
  $email->setSubject('Confirmation of changes regarding event: '.$event->name);
  $email->setContent("
Hello {$requestorName},

The following changes have been made to the event:

{$startDate} - {$endDate}
{$event->name}

{$changesString}


Regards,
Transportation Team
  ");
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($event->requestor) $email->addRecipient($event->requestor->emailAddress);
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $results[] = $email->sendText();


  // Email the driver(s)
  foreach ($event->drivers as $driverId) {
    if (!$driverId) continue;
    $driver = new User($driverId);
    $driverName = $driver->firstName;
    $email = new Email();
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->setSubject('Confirmation of changes regarding event: '.$event->name);
    $email->setContent("
Hello {$driverName},

The following changes have been made to the event:

{$startDate} - {$endDate}
{$event->name}

{$changesString}


Regards,
Transportation Team
    ");
    $email->addRecipient($driver->emailAddress, $driverName);
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $results[] = $email->sendText();
  }
  
}