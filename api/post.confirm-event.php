<?php
header('Content-Type: application/json');
require_once 'class.ics.php';
require_once 'class.config.php';
require_once 'class.email.php';
require_once 'class.event.php';
require_once 'class.user.php';
require_once 'class.email-templates.php';
require_once 'class.template.php';

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');
$json = json_decode(file_get_contents("php://input"));

$result = true;
$event = new Event($json->id);

if (!$event->confirmed) {
  
  $results[] = $event->confirm(userResponsibleForOperation: $me->getUsername());

  // Generate ics file
  include '../inc.event-ics.php';


  // Email to the requestor
  $template = new Template(EmailTemplates::get('Email Requestor New Event'));
  $templateData = [
    'name' => $event->requestor->firstName,
    'eventName' => $event->name,
    'startDate' => Date('m/d/Y', strtotime($event->startDate)),
    'endDate' => Date('m/d/Y', strtotime($event->endDate)),
  ];

  $email = new Email();
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($event->requestor) $email->addRecipient($event->requestor->emailAddress, $event->requestor->getName());
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Information regarding event: '.$event->name);
  $email->setContent($template->render($templateData));
  $email->sendText();


  // Email the drivers
  $template = new Template(EmailTemplates::get('Email Driver New Event'));
  foreach ($event->drivers as $driverId) {
    $driver = new User($driverId);
    $templateData = [
      'name' => $driver->firstName,
      'eventName' => $event->name,
      'startDate' => Date('m/d/Y', strtotime($event->startDate)),
      'endDate' => Date('m/d/Y', strtotime($event->endDate)),
    ];

    $email = new Email();
    $email->addRecipient($driver->emailAddress, $driver->getName());
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('An event has been assigned to you: '.$event->name);
    $email->setContent($template->render($templateData));
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->sendText();
  }
}

echo json_encode(['result' => $result]);
