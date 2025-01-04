<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.trip.php';
$trip = new Trip($json->tripId);
$trip->cancel($user->getUsername());

require_once 'class.email.php';
require_once 'class.email-templates.php';
require_once 'class.template.php';

$template = new Template(EmailTemplates::get('Email Manager Trip Request Cancellation'));
$managers = User::getManagers();
foreach ($managers as $manager) {
  $templateData = [
    'name' => $manager->first_name,
    'summary' => $trip->summary,
    'tripDate' => Date('m/d/Y', strtotime($trip->pickupDate)),
    'requestorEmail' => $user->emailAddress,
  ];

  $email = new Email();
  $email->addRecipient($manager->email_address, $manager->first_name.' '.$manager->last_name);
  $email->setSubject('Trip Request Cancellation: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->sendText();
}



die(json_encode(['result' => true]));