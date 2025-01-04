<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);
$result = $trip->delete(userResponsibleForOperation: $user->getUsername());

$trip = new Trip($_REQUEST['id']);
if ($trip->getId() && $trip->isConfirmed() && $trip->endDate > Date('Y-m-d H:i:s')) {
  notifyParticipants($trip);
}


die(json_encode(['result' => $result]));



function notifyParticipants(Trip $trip): void
{
  require_once 'class.config.php';
  require_once 'class.email.php';
  $config = Config::get('organization');
  $me = new User($_SESSION['user']->id);
  $tripDate = Date('m/d/Y', strtotime($trip->pickupDate));

  // Email to the requestor
  $requestorName = $trip->requestor->firstName;
  $email = new Email();
  $email->setSubject('Confirmation of cancellation/deletion of trip: '.$trip->summary);
  $email->setContent("
Hello {$requestorName},

The following trip has been cancelled/deleted:

{$tripDate}
{$trip->summary}


Regards,
Transportation Team
  ");
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($trip->requestor) $email->addRecipient($trip->requestor->emailAddress);
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $results[] = $email->sendText();


  // Email the driver
  $driverName = $trip->driver->firstName;
  $email = new Email();
  $email->setSubject('Confirmation of cancellation/deletion of trip: '.$trip->summary);
  $email->setContent("
Hello {$driverName},

The following trip has been cancelled/deleted:

{$tripDate}
{$trip->summary}


Regards,
Transportation Team
  ");
  $email->addRecipient($trip->driver->emailAddress, $driverName);
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $results[] = $email->sendText();

}