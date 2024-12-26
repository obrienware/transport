<?php
header('Content-Type: application/json');
require_once 'class.ics.php';
require_once 'class.config.php';
require_once 'class.email.php';
require_once 'class.utils.php';
require_once 'class.trip.php';
require_once 'class.user.php';

$me = new User($_SESSION['user']->id);
$config = Config::get('organization');
$json = json_decode(file_get_contents("php://input"));

$result = true;
$trip = new Trip($json->id);

if (!$trip->confirmed) {
  
  $results[] = $trip->confirm();
  // Create the trip sheets and email them to the respective people

  // Generate the driver sheet
  include '../inc.trip-driver-sheet.php';
  $filename1 = sys_get_temp_dir().'/'.$trip->tripId.'-trip-driver-sheet.pdf';
  $pdf->output('F', $filename1);

  // Generate the guest sheet
  include '../inc.trip-guest-sheet.php';
  $filename2 = sys_get_temp_dir().'/'.$trip->tripId.'-trip-guest-sheet.pdf';
  $pdf->output('F', $filename2);

  // Generate ics file
  $description = "";
  $description .= "Using ".$trip->vehicle->name.": ".$trip->vehiclePUOptions.' - '.$trip->vehicleDOOptions."\\n\\n";
  $description .= "PU ".$trip->guests." at ".$trip->puLocation->name."\\n\\n";
  $description .= "DO ".$trip->doLocation->name."\\n\\n";
  if ($trip->flightNumber) {
    $description .= "Flight ".$trip->airline->name." ".$trip->airline->flightNumberPrefix.$trip->flightNumber." ";
    if ($trip->ETA) {
      $description .= "ETA ".Date('g:ia', strtotime($trip->ETA))."\\n\\n";
    } else {
      $description .= "ETD ".Date('g:ia', strtotime($trip->ETD))."\\n\\n";
    }
  }
  $description .= "Contact: ".$trip->guest->getName()." ".$trip->guest->phoneNumber."\\n\\n";
  if ($trip->driverNotes) {
    $description .= "Additional Driver Notes:\\n";
    $description .= str_replace("\n", "\\n", $trip->driverNotes)."\\n";
  }
  
  $ics = new ICS([
    'dtstart' => $trip->startDate,
    'dtend' => $trip->endDate,
    'description' => $description,
    'summary' => $trip->summary,
    'location' => str_replace("\n", "\\n", $trip->puLocation->mapAddress),
    'url' => 'https://'.$_SERVER['HTTP_HOST'].'/print.trip-driver-sheet.php?id='.$trip->tripId
  ]);
  


  // Email to the requestor (include the guest sheet to pass along)
  $requestorName = $trip->requestor->firstName;
  $email = new Email();
  $email->addAttachment($filename2);
  $email->setSubject('Information regarding trip: '.$trip->summary);
  $email->setContent("
Hello {$requestorName},

The following trip has been scheduled:

{$trip->summary}

Please find your guest information sheet attached. Please have your guest scan the QR code on the sheet in order to recieve timely notifications relevant to their trip.

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
  $tripDate = Date('m/d/Y', strtotime($trip->pickupDate));
  $email = new Email();
  $email->addAttachment($filename1);
  $ical = $ics->to_string();
  $email->AddStringAttachment("$ical", "calendar-item.ics", "base64", "text/calendar; charset=utf-8; method=REQUEST");
  $email->setSubject('A trip has been assigned to you: '.$trip->summary);
  $email->setContent("
Hello {$driverName},

The following trip has been assigned to you:

{$tripDate}
{$trip->summary}

Please find your information sheet attached. This trip will automatically be tracked in your app.

Regards,
Transportation Team
  ");
  $email->addRecipient($trip->driver->emailAddress, $driverName);
  if ($config->email->copyAllEmail) $email->addBCC($config->email->copyAllEmail);
  $email->addReplyTo($me->emailAddress, $me->getName());
  $results[] = $email->sendText();

}

echo json_encode([
  'result' => $result,
  'results' => $results
]);

unlink($filename1);
unlink($filename2);