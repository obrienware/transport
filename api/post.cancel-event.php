<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.event.php';
$event = new Event($json->eventId);
$event->cancel($user->getUsername());

require_once 'class.email.php';
require_once 'class.email-templates.php';
require_once 'class.template.php';

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