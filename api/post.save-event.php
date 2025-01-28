<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Event;
use Transport\Location;
use Transport\Template;
use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$changes = [];
$driversToNotify = [];

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
      $driver = new User((int)$driverId);
      $drivers[] = $driver->getName();
      $driversToNotify[] = $driver;
    }
    $newDrivers = [];
    foreach ($json->drivers as $driverId) {
      $driver = new User((int)$driverId);
      $newDrivers[] = $driver->getName();
      $driversToNotify[] = $driver;
    }
    $changes[] = "The drivers were changed from \"".implode(', ', $drivers)."\" to \"".implode(', ', $newDrivers)."\"";
    $driversToNotify = getUniqueDrivers($driversToNotify);
  }
  if ($event->vehicles != $json->vehicles) {
    $vehicles = [];
    foreach ($event->vehicles as $vehicleId) {
      $vehicle = new Vehicle((int)$vehicleId);
      $vehicles[] = $vehicle->name;
    }
    $newVehicles = [];
    foreach ($json->vehicles as $vehicleId) {
      $vehicle = new Vehicle((int)$vehicleId);
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
  if ($changes) notifyParticipants($event, $changes, $driversToNotify);
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));






function notifyParticipants(Event $event, array $changes, ?array $driversToNotify = null): void
{

  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $changesString = '- '.implode("\n\n- ", $changes);

  // Generate ics file
  include '../inc.event-ics.php';
 
  
  // Email to the requestor
  $template = new Template(EmailTemplates::get('Email Requestor Event Change'));
  $templateData = [
    'name' => $event->requestor->firstName,
    'eventName' => $event->name,
    'startDate' => Date('m/d/Y', strtotime($event->startDate)),
    'endDate' => Date('m/d/Y', strtotime($event->endDate)),
    'changes' => $changesString,
  ];

  $email = new Email();
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($event->requestor) $email->addRecipient($event->requestor->emailAddress);
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Confirmation of changes regarding event: '.$event->name);
  $email->setContent($template->render($templateData));
  $email->sendText();


  // Email the driver(s)
  if (!$driversToNotify) return;
  $template = new Template(EmailTemplates::get('Email Driver Event Change'));
  foreach ($driversToNotify as $driver) {
    $templateData = [
      'name' => $driver->firstName,
      'eventName' => $event->name,
      'startDate' => Date('m/d/Y', strtotime($event->startDate)),
      'endDate' => Date('m/d/Y', strtotime($event->endDate)),
      'changes' => $changesString,
    ];

    $email = new Email();
    $email->addRecipient($driver->emailAddress, $driver->getName());
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding event: '.$event->name);
    $email->setContent($template->render($templateData));
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->sendText();
  }
  
}


function getUniqueDrivers(array $drivers): array
{
  $uniqueDrivers = [];
  $seenIds = [];

  foreach ($drivers as $driver) {
    $driverId = $driver->getId();
    if (!in_array($driverId, $seenIds)) {
      $uniqueDrivers[] = $driver;
      $seenIds[] = $driverId;
    }
  }

  return $uniqueDrivers;
}