<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Template;
use Transport\User;
use Transport\VehicleReservation;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$reservation = new VehicleReservation($id);
if (!$reservation->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Reservation not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $reservation->delete(userResponsibleForOperation: $user->getUsername());

$reservation = new VehicleReservation($id);
if ($reservation->getId() && $reservation->isConfirmed() && $reservation->endDateTime > Date('Y-m-d H:i:s')) {
  notifyParticipants($reservation);
}

die(json_encode(['result' => $result]));




function notifyParticipants(VehicleReservation $reservation): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $startDateTime = $reservation->startDateTime;
  $endDateTime = $reservation->endDateTime;

  // Email to the requestor
  if ($reservation->requestor) {
    $template = new Template(EmailTemplates::get('Email Requestor Vehicle Reservation Deleted'));
    $templateData = [
      'name' => $reservation->requestor->firstName,
      'guest' => $reservation->guest->getName(),
      'startDateTime' => $startDateTime,
      'endDateTime' => $endDateTime,
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor') {
      if ($reservation->requestor) $email->addRecipient($reservation->requestor->emailAddress);
    } else {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of cancellation/deletion of reservation for: '.$reservation->guest->getName());
    $email->setContent($template->render($templateData));
    $email->sendText();
  }
}