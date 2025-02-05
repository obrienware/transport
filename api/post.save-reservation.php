<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Config, Email, EmailTemplates, Guest, Template, User, Vehicle, VehicleReservation };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$changes = [];
$driversToNotify = [];

$reservation = new VehicleReservation($input->getInt('reservationId'));

// We are only interested in tracking changes to current and future reservation AND only if they are confirmed
if ($reservation->getId() && $reservation->isConfirmed() && $reservation->endDateTime > Date('Y-m-d H:i:s'))
{
  if ($reservation->reason != $input->getString('reason')) $changes[] = "The reservation reason was changed from \"{$reservation->reason}\" to \"{$input->getString('reason')}\"";
  if ($reservation->guestId != $input->getInt('guestId'))
  {
    if (!$reservation->guestId)
    {
      $newGuest = new Guest($input->getInt('guestId'));
      $changes[] = "Guest was assigned: \"{$newGuest->getName()}\"";
    }
    else
    {
      $guest = new Guest($reservation->guestId);
      $newGuest = new Guest($input->getInt('guestId'));
      $changes[] = "The guest was changed from \"{$guest->getName()}\" to \"{$newGuest->getName()}\"";
    }
  }
  if ($reservation->requestorId != $input->getInt('requestorId'))
  {
    if (!$reservation->requestorId)
    {
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    }
    else
    {
      $requestor = new User($reservation->requestorId);
      $newRequestor = new User($input->getInt('requestorId'));
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($reservation->startDateTime != $input->getString('startDateTime')) $changes[] = "The start date/time was changed from \"{$reservation->startDateTime}\" to \"{$input->getString('startDateTime')}\"";
  if ($reservation->endDateTime != $input->getString('endDateTime')) $changes[] = "The end date/time was changed from \"{$reservation->endDate}\" to \"{$input->getString('endDateTime')}\"";
  if ($reservation->vehicleId != $input->getInt('vehicleId'))
  {
    if (!$reservation->vehicleId)
    {
      $newVehicle = new Vehicle($input->getInt('vehicleId'));
      $changes[] = "Vehicle assigned: {$newVehicle->name}";
    }
    else
    {
      $vehicle = new Vehicle($reservation->vehicleId);
      $newVehicle = new Vehicle($input->getInt('vehicleId'));
      $changes[] = "The vehicle changed from \"{$vehicle->name}\" to \"{$newVehicle->name}\"";
    }
  }
}


$reservation->reason = $input->getString('reason');
$reservation->guestId = $input->getInt('guestId');
$reservation->requestorId = $input->getInt('requestorId');
$reservation->startDateTime = $input->getString('startDateTime');
$reservation->endDateTime = $input->getString('endDateTime');
$reservation->vehicleId = $input->getInt('vehicleId');

if ($reservation->save(userResponsibleForOperation: $user->getUsername()))
{
  $result = $reservation->getId();
  if ($changes) notifyParticipants($reservation, $changes);
  die(json_encode(['result' => $result]));
}
die(json_encode([
  'result' => false,
  'error' => $reservation->getLastError()
]));


/**
 * Notify the participants of the changes
 * 
 * @param VehicleReservation $reservation
 * @param array $changes
 * @return void
 */
function notifyParticipants(VehicleReservation $reservation, array $changes): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $changesString = '- ' . implode("\n\n- ", $changes);

  // Email to the requestor
  if ($reservation->requestorId)
  {
    $template = new Template(EmailTemplates::get('Email Requestor Vehicle Reservation Change'));
    $templateData = [
      'name' => $reservation->requestor->firstName,
      'guest' => $reservation->guest->getName(),
      'vehicle' => $reservation->vehicle->name,
      'startDateTime' => Date('m/d/Y', strtotime($reservation->startDateTime)),
      'endDateTime' => Date('m/d/Y', strtotime($reservation->endDateTime)),
      'changes' => $changesString,
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor')
    {
      if ($reservation->requestor) $email->addRecipient($reservation->requestor->emailAddress);
    }
    else
    {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding vehicle reservation for: ' . $reservation->guest->getName());
    $email->setContent($template->render($templateData));
    $email->sendText();
  }
}
