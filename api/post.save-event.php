<?php

declare(strict_types=1);

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
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$changes = [];
$driversToNotify = [];

$event = new Event($input->getInt('id'));

if ($event->getId() && $event->isConfirmed() && $event->endDate > Date('Y-m-d H:i:s'))
{
  // We are only interested in tracking changes to existing events AND if the event is confirmed
  if ($event->name != $input->getString('name')) $changes[] = "The event name was changed from \"{$event->name}\" to \"{$input->getString('name')}\"";
  if ($event->requestorId != $input->getInt('requestorId'))
  {
    if (!$event->requestorId)
    {
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    }
    else
    {
      $requestor = new User($event->requestorId);
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($event->locationId != $input->getInt('locationId'))
  {
    if (!$event->locationId)
    {
      $newLocation = new Location($input->getInt('locationId'));
      $changes[] = "Location was assigned to \"{$newLocation->name}\"";
    }
    else
    {
      $location = new Location($event->locationId);
      $newLocation = new Location($input->getInt('locationId'));
      $changes[] = "The location was changed from \"{$location->name}\" to \"{$newLocation->name}\"";
    }
  }
  if ($event->startDate != $input->getString('startDate')) $changes[] = "The start date was changed from \"{$event->startDate}\" to \"{$input->getString('startDate')}\"";
  if ($event->endDate != $input->getString('endDate')) $changes[] = "The end date was changed from \"{$event->endDate}\" to \"{$input->getString('endDate')}\"";
  if ($event->drivers != $input->getArray('drivers'))
  {
    $drivers = [];
    foreach ($event->drivers as $driverId)
    {
      if (!$driverId) continue;
      $driver = new User((int)$driverId);
      $drivers[] = $driver->getName();
      $driversToNotify[] = $driver;
    }
    $newDrivers = [];
    foreach ($input->getArray('drivers') as $driverId)
    {
      $driver = new User((int)$driverId);
      $newDrivers[] = $driver->getName();
      $driversToNotify[] = $driver;
    }
    $changes[] = "The drivers were changed from \"" . implode(', ', $drivers) . "\" to \"" . implode(', ', $newDrivers) . "\"";
    $driversToNotify = getUniqueDrivers($driversToNotify);
  }
  if ($event->vehicles != $input->getArray('vehicles'))
  {
    $vehicles = [];
    foreach ($event->vehicles as $vehicleId)
    {
      $vehicle = new Vehicle((int)$vehicleId);
      $vehicles[] = $vehicle->name;
    }
    $newVehicles = [];
    foreach ($input->getArray('vehicles') as $vehicleId)
    {
      $vehicle = new Vehicle((int)$vehicleId);
      $newVehicles[] = $vehicle->name;
    }
    $changes[] = "The vehicles were changed from \"" . implode(', ', $vehicles) . "\" to \"" . implode(', ', $newVehicles) . "\"";
  }
  if ($event->notes != $input->getString('notes'))
  {
    if (!$event->notes)
    {
      $changes[] = "Notes were added: \n" . $input->getString('notes');
    }
    else
    {
      $changes[] = "Notes were changed: \n" . $input->getString('notes');
    }
  }
}

$event->name = $input->getString('name');
$event->requestorId = $input->getInt('requestorId');
$event->locationId = $input->getInt('locationId');
$event->startDate = $input->getString('startDate');
$event->endDate = $input->getString('endDate');

$event->drivers = [];
$event->vehicles = [];

$event->notes = $input->getString('notes');
// The html control we use to select the drivers and vehicles sends an array of strings, so we need to convert them to integers
if ($input->getArray('drivers'))
{
  $drivers = [];
  foreach ($input->getArray('drivers') as $driver)
  {
    $drivers[] = (int)$driver;
  }
  $event->drivers = $drivers;
}
if ($input->getArray('vehicles'))
{
  $vehicles = [];
  foreach ($input->getArray('vehicles') as $vehicle)
  {
    $vehicles[] = (int)$vehicle;
  }
  $event->vehicles = $vehicles;
}

if ($event->save(userResponsibleForOperation: $user->getUsername()))
{
  if ($changes) notifyParticipants($event, $changes, $driversToNotify);
  exit(json_encode(['result' => $event->getId()]));
}
exit(json_encode(['result' => false, 'error' => $event->getLastError()]));






function notifyParticipants(Event $event, array $changes, ?array $driversToNotify = null): void
{

  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $changesString = '- ' . implode("\n\n- ", $changes);


  // Email to the requestor
  if ($event->requestor)
  {
    $template = new Template(EmailTemplates::get('Email Requestor Event Change'));
    $templateData = [
      'name' => $event->requestor->firstName,
      'eventName' => $event->name,
      'startDate' => Date('m/d/Y', strtotime($event->startDate)),
      'endDate' => Date('m/d/Y', strtotime($event->endDate)),
      'changes' => $changesString,
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor')
    {
      if ($event->requestor) $email->addRecipient($event->requestor->emailAddress);
    }
    else
    {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding event: ' . $event->name);
    $email->setContent($template->render($templateData));
    $email->sendText();
  }


  // Email the driver(s)
  if (!$driversToNotify) return;

  // Generate ics file
  include '../inc.event-ics.php';

  $template = new Template(EmailTemplates::get('Email Driver Event Change'));
  foreach ($driversToNotify as $driver)
  {
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
    $email->setSubject('Confirmation of changes regarding event: ' . $event->name);
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

  foreach ($drivers as $driver)
  {
    $driverId = $driver->getId();
    if (!in_array($driverId, $seenIds))
    {
      $uniqueDrivers[] = $driver;
      $seenIds[] = $driverId;
    }
  }

  return $uniqueDrivers;
}
