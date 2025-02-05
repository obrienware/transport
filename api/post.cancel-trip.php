<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Email, EmailTemplates, Template, Trip, User };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$trip = new Trip($input->getInt('tripId'));
$trip->cancel($user->getUsername());

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



exit(json_encode(['result' => true]));