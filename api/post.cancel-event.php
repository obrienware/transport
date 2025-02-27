<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Email, EmailTemplates, Event, Template, User };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$event = new Event($input->getInt('eventId'));
$event->cancel($user->getUsername());

$template = new Template(EmailTemplates::get('Email Manager Event Request Cancellation'));
$managers = User::getManagers();
foreach ($managers as $manager)
{
  $templateData = [
    'name' => $manager->first_name,
    'summary' => $event->name,
    'startDate' => Date('m/d/Y', strtotime($event->startDate)),
    'endDate' => Date('m/d/Y', strtotime($event->endDate)),
    'requestorEmail' => $user->emailAddress,
  ];

  $email = new Email();
  $email->addRecipient($manager->email_address, $manager->first_name . ' ' . $manager->last_name);
  $email->setSubject('Event Request Cancellation: ' . $event->name);
  $email->setContent($template->render($templateData));
  $email->sendText();
}


exit(json_encode(['result' => true]));
