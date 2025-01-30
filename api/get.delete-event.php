<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Event;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Template;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$event = new Event($id);
$result = $event->delete(userResponsibleForOperation: $user->getUsername());

$event = new Event($id);
if ($event->getId() && $event->isConfirmed() && $event->endDate > Date('Y-m-d H:i:s')) {
  notifyParticipants($event);
}

die(json_encode(['result' => $result]));




function notifyParticipants(Event $event): void
{
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $startDate = Date('m/d/Y', strtotime($event->startDate));
  $endDate = Date('m/d/Y', strtotime($event->endDate));

  // Email to the requestor
  if ($event->requestor) {
    $template = new Template(EmailTemplates::get('Email Requestor Event Deleted'));
    $templateData = [
      'name' => $event->requestor->firstName,
      'eventName' => $event->name,
      'startDate' => $startDate,
      'endDate' => $endDate,
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor') {
      if ($event->requestor) $email->addRecipient($event->requestor->emailAddress);
    } else {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of cancellation/deletion of event: '.$event->name);
    $email->setContent($template->render($templateData));
    $email->sendText();
  }

  // Email the driver(s)
  if (!$event->drivers) return;
  $template = new Template(EmailTemplates::get('Email Driver Event Deleted'));
  foreach ($event->drivers as $driverId) {
    if (!$driverId) continue;
    $driver = new User($driverId);
    $templateData = [
      'name' => $driver->firstName,
      'eventName' => $event->name,
      'startDate' => $startDate,
      'endDate' => $endDate,
    ];
    $email = new Email();
    $email->addRecipient($driver->emailAddress, $driver->getName());
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Confirmation of cancellation/deletion of event: '.$event->name);
    $email->setContent($template->render($templateData));
    $email->sendText();
  }

}