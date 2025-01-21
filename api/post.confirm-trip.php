<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Email;
use Transport\EmailTemplates;
use Transport\Template;
use Transport\Trip;
use Transport\User;

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');
$json = json_decode(file_get_contents("php://input"));

$result = true;
$trip = new Trip($json->id);

if (!$trip->confirmed) {
  
  $trip->confirm($me->getUsername());

  // Generate the driver sheet
  include '../inc.trip-driver-sheet.php';
  $filename1 = sys_get_temp_dir().'/'.$trip->getId().'-trip-driver-sheet.pdf';
  $pdf->output('F', $filename1);

  // Generate the guest sheet
  include '../inc.trip-guest-sheet.php';
  $filename2 = sys_get_temp_dir().'/'.$trip->getId().'-trip-guest-sheet.pdf';
  $pdf->output('F', $filename2);

  // Generate ics file
  include '../inc.trip-ics.php';


  // Email to the requestor (include the guest sheet to pass along)
  $template = new Template(EmailTemplates::get('Email Requestor New Trip'));
  $templateData = [
    'name' => $trip->requestor->firstName,
    'tripSummary' => $trip->summary,
  ];
  
  $email = new Email();
  if ($config->email->sendRequestorMessagesTo == 'requestor') {
    if ($trip->requestor) $email->addRecipient($trip->requestor->emailAddress, $trip->requestor->getName());
  } else {
    $email->addRecipient($config->email->sendRequestorMessagesTo);
  }
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('Information regarding trip: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->addAttachment($filename2);
  $email->sendText();


  // Email the driver
  $template = new Template(EmailTemplates::get('Email Driver New Trip'));
  $templateData = [
    'name' => $trip->driver->firstName,
    'tripDate' => Date('m/d/Y', strtotime($trip->pickupDate)),
    'tripSummary' => $trip->summary,
  ];

  $email = new Email();
  $email->addRecipient($trip->driver->emailAddress, $trip->driver->getName());
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $email->setSubject('A trip has been assigned to you: '.$trip->summary);
  $email->setContent($template->render($templateData));
  $email->addAttachment($filename1);
  $ical = $ics->to_string();
  $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
  $email->sendText();

}

echo json_encode(['result' => $result]);

unlink($filename1);
unlink($filename2);