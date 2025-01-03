<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.event.php';
$event = new Event($_REQUEST['id']);
$result = $event->delete(userResponsibleForOperation: $user->getUsername());

$event = new Event($_REQUEST['id']);
if ($event->getId() && $event->isConfirmed() && $event->endDate > Date('Y-m-d H:i:s')) {
  notifyParticipants($event);
}

die(json_encode(['result' => $result]));




function notifyParticipants(Event $event): void
{
  require_once 'class.config.php';
  require_once 'class.email.php';
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);

  // Email to the requestor
  $requestorName = $event->requestor->firstName;
  $email = new Email();
  $email->setSubject('Confirmation of cancellation/deletion of event: '.$event->name);
  $email->setContent("
Hello {$requestorName},

The following event has been cancelled/deleted:

{$startDate} - {$endDate}
{$event->name}


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
    $email->setSubject('Confirmation of cancellation/deletion of event: '.$event->name);
    $email->setContent("
Hello {$driverName},

The following event has been cancelled/deleted:

{$startDate} - {$endDate}
{$event->name}


Regards,
Transportation Team
    ");
    $email->addRecipient($driver->emailAddress, $driverName);
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $results[] = $email->sendText();
  }

}