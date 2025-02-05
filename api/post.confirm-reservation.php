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
use Generic\JsonInput;

$input = new JsonInput();

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');

$result = true;
$reservation = new VehicleReservation($input->getInt('id'));

if (!$reservation->isConfirmed()) {
  
  $results[] = $reservation->confirm(userResponsibleForOperation: $me->getUsername());

  // Email to the requestor
  if ($reservation->requestor) {    
    $template = new Template(EmailTemplates::get('Email Requestor New Reservation'));
    $templateData = [
      'name' => $reservation->requestor->firstName,
      'guest' => $reservation->guest->getName(),
      'reason' => $reservation->reason,
      'startDateTime' => Date('m/d/Y', strtotime($reservation->startDateTime)),
      'endDateTime' => Date('m/d/Y', strtotime($reservation->endDateTime)),
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor') {
      if ($reservation->requestor) $email->addRecipient($reservation->requestor->emailAddress, $reservation->requestor->getName());
    } else {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Information regarding vehicle reservation for: '.$reservation->guest->getName());
    $email->setContent($template->render($templateData));
    $email->sendText();
  }
}

echo json_encode(['result' => $result]);
