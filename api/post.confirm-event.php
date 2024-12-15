<?php
header('Content-Type: application/json');
require_once 'class.ics.php';
require_once 'class.config.php';
require_once 'class.email.php';
require_once 'class.event.php';
require_once 'class.user.php';

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');
$json = json_decode(file_get_contents("php://input"));

$result = true;
$event = new Event($json->id);

if (!$event->confirmed) {
  
  $results[] = $event->confirm();

  // Generate ics file
  $ics = new ICS([
    'dtstart' => $event->startDate,
    'dtend' => $event->endDate,
    'description' => $event->notes,
    'summary' => $event->name,
    // 'location' => str_replace("\n", "\\n", $trip->puLocation->mapAddress),
    // 'url' => 'https://'.$_SERVER['HTTP_HOST'].'/print.trip-driver-sheet.php?id='.$trip->tripId
  ]);
  if ($event->location) $ics->set('location', str_replace("\n", "\\n", $event->location->mapAddress));
  


  // Email to the requestor (include the guest sheet to pass along)
  $requestorName = $event->requestor->firstName;
  $startDate = Date('m/d/Y', strtotime($event->startDate));
  $endDate = Date('m/d/Y', strtotime($event->endDate));
  $email = new Email();
  $email->setSubject('Information regarding event: '.$event->name);
  $email->setContent("
Hello {$requestorName},

The following event has been scheduled:

{$startDate} - {$endDate}
{$event->name}

Regards,
Transportation Team
  ");
  if ($config->sendRequestorMessagesTo == 'requestor') {
    if ($event->requestor) $email->addRecipient($event->requestor->emailAddress);
  } else {
    $email->addRecipient($config->sendRequestorMessagesTo);
  }
  if ($config->copyAllEmail) $email->addBCC($config->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $results[] = $email->sendText();


  // Email the drivers
  foreach ($event->drivers as $driverId) {
    $driver = new User($driverId);
    $driverName = $driver->firstName;
    $startDate = Date('m/d/Y', strtotime($event->startDate));
    $endDate = Date('m/d/Y', strtotime($event->endDate));
    $email = new Email();
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->setSubject('An event has been assigned to you: '.$event->name);
    $email->setContent("
Hello {$driverName},

The following event has been assigned to you:

{$startDate} - {$endDate}
{$event->name}

Regards,
Transportation Team
    ");
    $email->addRecipient($driver->emailAddress, $driverName);
    if ($config->copyAllEmail) $email->addBCC($config->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $results[] = $email->sendText();
    }
}

echo json_encode([
  'result' => $result,
  'results' => $results
]);
