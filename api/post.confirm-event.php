<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Config, Email, EmailTemplates, Event, Template, User };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');

$result = true;
$event = new Event($input->getInt('id'));

if (!$event->isConfirmed())
{

  $results[] = $event->confirm(userResponsibleForOperation: $me->getUsername());

  // Email to the requestor
  if ($event->requestor)
  {
    $template = new Template(EmailTemplates::get('Email Requestor New Event'));
    $templateData = [
      'name' => $event->requestor->firstName,
      'eventName' => $event->name,
      'startDate' => Date('m/d/Y', strtotime($event->startDate)),
      'endDate' => Date('m/d/Y', strtotime($event->endDate)),
    ];

    $email = new Email();
    if ($config->email->sendRequestorMessagesTo == 'requestor')
    {
      if ($event->requestor) $email->addRecipient($event->requestor->emailAddress, $event->requestor->getName());
    }
    else
    {
      $email->addRecipient($config->email->sendRequestorMessagesTo);
    }
    if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
    $email->addReplyTo($me->emailAddress, $me->getName());
    $email->setSubject('Information regarding event: ' . $event->name);
    $email->setContent($template->render($templateData));
    $email->sendText();
  }


  // Email the drivers
  if (!$event->drivers) exit(json_encode(['result' => $result]));
  $template = new Template(EmailTemplates::get('Email Driver New Event'));

  // Generate ics file
  include '../inc.event-ics.php';

  // Generate the driver sheet
  include '../inc.event-driver-sheet.php';
  $filename1 = sys_get_temp_dir().'/'.$event->getId().'-event-driver-sheet.pdf';
  $pdf->output('F', $filename1);

  foreach ($event->drivers as $driverId)
  {
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
    $email->setSubject('An event has been assigned to you: ' . $event->name);
    $email->setContent($template->render($templateData));
    $email->addAttachment($filename1);
    $ical = $ics->to_string();
    $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
    $email->sendText();
  }
  unlink($filename1);
}

echo json_encode(['result' => $result]);
