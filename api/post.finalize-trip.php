<?php
header('Content-Type: application/json');
require_once 'class.email.php';
require_once 'class.utils.php';
require_once 'class.trip.php';
$json = json_decode(file_get_contents("php://input"));

$result = true;
$trip = new Trip($json->id);

if ($trip->finalized == 0) {
  
  $results[] = $trip->finalize();
  // Create the trip sheets and email them to the respective people

  // Generate the driver sheet
  include '../inc.trip-driver-sheet.php';
  $filename1 = $_SERVER['DOCUMENT_ROOT'].'/tripsheets/'.$trip->tripId.'-trip-driver-sheet.pdf';
  $pdf->output('F', $filename1);

  // Generate the guest sheet
  include '../inc.trip-guest-sheet.php';
  $filename2 = $_SERVER['DOCUMENT_ROOT'].'/tripsheets/'.$trip->tripId.'-trip-guest-sheet.pdf';
  $pdf->output('F', $filename2);

  // Email to the requestor (include the guest sheet to pass along)
  $requestorName = $trip->requestor->firstName;
  $email = new Email();
  $email->mail->addAttachment($filename2);
  $email->setSubject('Information regarding trip: '.$trip->summary);
  $email->setContent("
Hello {$requestorName},

The following trip has been scheduled:

{$trip->summary}

Please find attached your guest information sheet.

Regards,
Transportation Team
  ");
  // TODO: When ready to go live - send this to the requestor instead.
  $email->addRecipient('richard@obrienware.com', 'Richard');
  $results[] = $email->sendText();


  // Email the driver
  $driverName = $trip->driver->firstName;
  $tripDate = Date('m/d/Y', strtotime($trip->pickupDate));
  $email = new Email();
  $email->mail->addAttachment($filename1);
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
  $results[] = $email->sendText();

}

echo json_encode([
  'result' => $result,
  'results' => $results
]);