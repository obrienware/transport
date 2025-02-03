<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Guest;
use Transport\Template;
use Transport\User;
use Transport\Vehicle;
use Transport\VehicleReservation;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$changes = [];
$driversToNotify = [];

$reservation = new VehicleReservation($json->reservationId);

// We are only interested in tracking changes to current and future reservation AND only if they are confirmed
if ($reservation->getId() && $reservation->isConfirmed() && $reservation->endDateTime > Date('Y-m-d H:i:s')) {
  if ($reservation->reason != $json->reason) $changes[] = "The reservation reason was changed from \"{$reservation->reason}\" to \"{$json->reason}\"";
  if ($reservation->guestId != $json->guestId) {
    if (!$reservation->guestId) {
      $newGuest = new Guest($json->guestId);
      $changes[] = "Guest was assigned: \"{$newGuest->getName()}\"";
    } else {
      $guest = new Guest($reservation->guestId);
      $newGuest = new Guest($json->guestId);
      $changes[] = "The guest was changed from \"{$guest->getName()}\" to \"{$newGuest->getName()}\"";
    }
  }
  if ($reservation->requestorId != $json->requestorId) {
    if (!$reservation->requestorId) {
      $newRequestor = new User($json->requestorId);
      $changes[] = "Requestor was assigned: \"{$newRequestor->getName()}\"";
    } else {
      $requestor = new User($event->requestorId);
      $newRequestor = new User($json->requestorId);
      $changes[] = "The requestor was changed from \"{$requestor->getName()}\" to \"{$newRequestor->getName()}\"";
    }
  }
  if ($reservation->startDateTime != $json->startDateTime) $changes[] = "The start date/time was changed from \"{$reservation->startDateTime}\" to \"{$json->startDateTime}\"";
  if ($reservation->endDateTime != $json->endDateTime) $changes[] = "The end date/time was changed from \"{$reservation->endDate}\" to \"{$json->endDateTime}\"";
  if ($reservation->vehicleId != $json->vehicleId) {
    if (!$trreservationireservationp->vehicleId) {
      $newVehicle = new Vehicle($json->vehicleId);
      $changes[] = "Vehicle assigned: {$newVehicle->name}";
    } else {
      $vehicle = new Vehicle($reservation->vehicleId);
      $newVehicle = new Vehicle($json->vehicleId);
      $changes[] = "The vehicle changed from \"{$vehicle->name}\" to \"{$newVehicle->name}\"";
    }
  }
}

$reservation->reason = $json->reason;
$reservation->guestId = $json->guestId;
$reservation->requestorId = $json->requestorId;
$reservation->startDateTime = $json->startDateTime;
$reservation->endDateTime = $json->endDateTime;
$reservation->vehicleId = $json->vehicleId;

if ($reservation->save(userResponsibleForOperation: $user->getUsername())) {
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
  $changesString = '- '.implode("\n\n- ", $changes);

  // Email to the requestor
  if ($reservation->requestorId) {
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
    if ($config->email->sendRequestorMessagesTo == 'requestor') {
      if ($reservation->requestor) $email->addRecipient($reservation->requestor->emailAddress);
    } else {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of changes regarding vehicle reservation for: '.$reservation->guest->getName());
    $email->setContent($template->render($templateData));
    $email->sendText();  
  }
}
