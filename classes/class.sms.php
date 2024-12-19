<?php
require_once 'class.config.php';
$config = Config::get('system');

require_once 'class.utils.php';
require_once 'class.data.php';
if (!isset($db)) $db = new data();

class SMS
{
  static public function send (string $recipient, string $message)
  {
    global $db;
    global $config;
    $tel = SMS::formattedPhoneNumber($recipient);
    // Only if the recipient has opted in to recieve messages
    if ($ok = $db->get_row("SELECT * FROM opt_in_text WHERE tel = :tel", ['tel' => $tel])) {
      // The following is for using Twilio
      $data = [
        'To' => $tel,
        'From' => $config->textFromNumber,
        'Body' => $message
      ];
      $result = Utils::callApi(
        'POST', 
        "https://api.twilio.com/2010-04-01/Accounts/{$_ENV['TWILIO_ACCOUNT_SID']}/Messages.json",
        $data, [
          'username' => $_ENV['TWILIO_ACCOUNT_SID'],
          'password' => $_ENV['TWILIO_AUTH_TOKEN']
        ]
      );
      $db->query(
        "INSERT INTO text_out SET datetimestamp = NOW(), recipient = :recipient, message = :message, result = :result",
        [
          'recipient' => $tel,
          'message' => $message,
          'result' => $result
        ]
      );      
      return $result;

      // The following is for using ClickSend
      $messageObj = (object) ['messages' => [['body' => $message, 'to' => $tel]]];
      $data = json_encode($messageObj);
      $result = Utils::callApi('POST', 'https://rest.clicksend.com/v3/sms/send' , $data, [
        'username' => $_ENV['CLICKSEND_USERNAME'],
        'password' => $_ENV['CLICKSEND_PASSWORD']
      ], ['Content-Type: application/json']);
      $db->query(
        "INSERT INTO text_out SET datetimestamp = NOW(), recipient = :recipient, message = :message, result = :result",
        [
          'recipient' => $recipient,
          'message' => $message,
          'result' => $result
        ]
      );      
      return $result;
    }
    return false;
  }

  static public function optIn (string $recipient)
  {
    global $db;
    $phone = SMS::formattedPhoneNumber($recipient);
    $sql = "REPLACE INTO opt_in_text SET tel = :tel, opt_in = NOW()";
    $data = ['tel' => $phone];
    $result = $db->query($sql, $data);
    // echo json_encode(['result' => $result]);
    // Send a text confirmation
    $message = "Thank you! You have opted in to receiving timely reminders from AWM/Charis Transport. To opt out at any point, simply reply STOP.";
    return SMS::send($phone, $message);
  }

  static public function optOut (string $recipient)
  {
    global $db;
    $tel = SMS::formattedPhoneNumber($recipient);
    $sql = "UPDATE opt_in_text SET opt_out = NOW() WHERE tel = :tel";
    $data = ['tel' => $tel];
    return $db->query($sql, $data);  
  }

  static public function formattedPhoneNumber(string $phoneNumber)
  {
    $phoneNumber = str_replace('+1', '', $phoneNumber);
    if (str_contains($phoneNumber, '+')) {
      return str_replace(' ', '', $phoneNumber); // Just remove the spaces
    } else {
      return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $phoneNumber);
    }  
  }
}