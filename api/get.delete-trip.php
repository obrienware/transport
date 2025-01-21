<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Template;
use Transport\Trip;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$trip = new Trip($id);
$result = $trip->delete(userResponsibleForOperation: $user->getUsername());

$trip = new Trip($id);
if ($trip->getId() && $trip->isConfirmed() && $trip->endDate > Date('Y-m-d H:i:s')) {
  notifyParticipants($trip);
}


die(json_encode(['result' => $result]));



function notifyParticipants(Trip $trip): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $tripDate = Date('m/d/Y', strtotime($trip->pickupDate));

  // Email to the requestor
  $template = new Template(EmailTemplates::get('Email Requestor Trip Deleted'));
  $templateData = [
    'name' => $trip->requestor->firstName,
    'tripSummary' => $trip->summary,
    'tripDate' => $tripDate,
  ];

  $email = new Email();
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($trip->requestor) $email->addRecipient($trip->requestor->emailAddress, $trip->requestor->getName());
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Confirmation of cancellation/deletion of trip: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->sendText();


  // Email the driver
  $template = new Template(EmailTemplates::get('Email Driver Trip Deleted'));
  $templateData = [
    'name' => $trip->driver->firstName,
    'tripDate' => $tripDate,
    'tripSummary' => $trip->summary,
  ];

  $email = new Email();
  $email->addRecipient($trip->driver->emailAddress, $trip->driver->getName());
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Confirmation of cancellation/deletion of trip: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->sendText();

}