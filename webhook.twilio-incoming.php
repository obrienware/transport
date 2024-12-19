<?php
header('Content-Type: application/json');
require_once 'class.sms.php';
require_once 'class.data.php';
$db = new data();
$sql = "
  INSERT INTO webhook_twilio SET
    ToCountry = :ToCountry,
    ToState = :ToState,
    SmsMessageSid = :SmsMessageSid,
    NumMedia = :NumMedia,
    ToCity = :ToCity,
    `To` = :To,
    FromZip = :FromZip,
    SmsSid = :SmsSid,
    OptOutType = :OptOutType,
    `From` = :From,
    FromState = :FromState,
    SmsStatus = :SmsStatus,
    FromCity = :FromCity,
    Body = :Body,
    FromCountry = :FromCountry,
    MessagingServiceSid = :MessagingServiceSid,
    ToZip = :ToZip,
    NumSegments = :NumSegments,
    MessageSid = :MessageSid,
    AccountSid = :AccountSid,
    ApiVersion = :ApiVersion
";
$data = [
  'ToCountry' => $_POST['ToCountry'],
  'ToState' => $_POST['ToState'],
  'SmsMessageSid' => $_POST['SmsMessageSid'],
  'NumMedia' => $_POST['NumMedia'],
  'ToCity' => $_POST['ToCity'],
  'To' => $_POST['To'],
  'FromZip' => $_POST['FromZip'],
  'SmsSid' => $_POST['SmsSid'],
  'OptOutType' => $_POST['OptOutType'],
  'From' => $_POST['From'],
  'FromState' => $_POST['FromState'],
  'SmsStatus' => $_POST['SmsStatus'],
  'FromCity' => $_POST['FromCity'],
  'Body' => $_POST['Body'],
  'FromCountry' => $_POST['FromCountry'],
  'MessagingServiceSid' => $_POST['MessagingServiceSid'],

  'ToZip' => $_POST['ToZip'],
  'NumSegments' => $_POST['NumSegments'],
  'MessageSid' => $_POST['MessageSid'],
  'AccountSid' => $_POST['AccountSid'],
  'ApiVersion' => $_POST['ApiVersion'],

];
$id = $db->query($sql, $data);

// If someone responds with "STOP" to stop sending them messages.
$pos = stripos($_POST['Body'], 'stop');
if ($pos !== false && $pos <= 5) {
  SMS::optOut($_POST['From']);
}
