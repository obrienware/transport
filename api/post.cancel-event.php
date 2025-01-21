<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Email;
use Transport\EmailTemplates;
use Transport\Event;
use Transport\Template;
use Transport\User;

$json = json_decode(file_get_contents("php://input"));
$user = new User($_SESSION['user']->id);

$event = new Event($json->eventId);
$event->cancel($user->getUsername());

$template = new Template(EmailTemplates::get('Email Manager Event Request Cancellation'));
$managers = User::getManagers();
foreach ($managers as $manager) {
  $templateData = [
    'name' => $manager->first_name,
    'summary' => $event->name,
    'startDate' => Date('m/d/Y', strtotime($event->endDate)),
    'endDate' => Date('m/d/Y', strtotime($event->endDate)),
    'requestorEmail' => $user->emailAddress,
  ];

  $email = new Email();
  $email->addRecipient($manager->email_address, $manager->first_name.' '.$manager->last_name);
  $email->setSubject('Event Request Cancellation: '.$event->name);
  $email->setContent($template->render($templateData));
  $email->sendText();
}


die(json_encode(['result' => true]));