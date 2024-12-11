<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$sql = "
  INSERT INTO webhook_clicksend SET
    originalsenderid = :originalsenderid,
    body = :body,
    message = :message,
    sms = :sms,
    custom_string = :custom_string,
    `to` = :to,
    original_message_id = :original_message_id,
    originalmessageid = :originalmessageid,
    customstring = :customstring,
    `from` = :from,
    originalmessage = :originalmessage,
    user_id = :user_id,
    subaccount_id = :subaccount_id,
    original_body = :original_body,
    timestamp = :timestamp,
    message_id =:message_id
";
$data = [
  'originalsenderid' => $_POST['originalsenderid'],
  'body' => $_POST['body'],
  'message' => $_POST['message'],
  'sms' => $_POST['sms'],
  'custom_string' => $_POST['custom_string'],
  'to' => $_POST['to'],
  'original_message_id' => $_POST['original_message_id'],
  'originalmessageid' => $_POST['originalmessageid'],
  'customstring' => $_POST['customstring'],
  'from' => $_POST['from'],
  'originalmessage' => $_POST['originalmessage'],
  'user_id' => $_POST['user_id'],
  'subaccount_id' => $_POST['subaccount_id'],
  'original_body' => $_POST['original_body'],
  'timestamp' => $_POST['timestamp'],
  'message_id' => $_POST['message_id']
];
$id = $db->query($sql, $data);

// If someone responds with "STOP" to stop sending them messages.
$pos = stripos($_POST['body'], 'stop');
if ($pos !== false && $pos <= 5) {
  $sql = "UPDATE opt_in_text SET opt_out = NOW() WHERE tel = :tel";
  $data = ['tel' => formattedPhoneNumber(str_replace('+1', '', $_POST['sms']))];
  $db->query($sql, $data);
}

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}