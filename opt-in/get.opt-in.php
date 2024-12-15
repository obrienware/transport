<?php
// header('Content-Type: application/json');
$phone = formattedPhoneNumber($_REQUEST['phone']);

require_once 'class.utils.php';

require_once 'class.data.php';
$db = new data();
$sql = "REPLACE INTO opt_in_text SET tel = :tel, opt_in = NOW()";
$data = ['tel' => $phone];
$result = $db->query($sql, $data);
echo json_encode(['result' => $result]);

// Send a text confirmation
$body = "Thank you! You have opted in to receiving timely reminders from AWM/Charis Transport. To opt out at any point, simply reply STOP.";
$to = $phone;

$message = (object) [
  'messages' => [
    [
      'body' => $body,
      'to' => $to
    ]
  ]
];
$data = json_encode($message);
$result = Utils::callApi('POST', 'https://rest.clicksend.com/v3/sms/send' , $data, [
  'username' => $_ENV['CLICKSEND_USERNAME'],
  'password' => $_ENV['CLICKSEND_PASSWORD']
], [
  'Content-Type: application/json'
]);
$db->query(
  "INSERT INTO text_out SET datetimestamp = NOW(), recipient = :recipient, message = :message, result = :result",
  [
    'recipient' => $to,
    'message' => $body,
    'result' => $result
  ]
);

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}