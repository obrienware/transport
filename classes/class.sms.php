<?php
require_once 'class.config.php';
global $config;
$config = Config::get('system');

require_once 'class.utils.php';
require_once 'class.data.php';
global $db;
if (!isset($db)) $db = new data();

class SMS
{

  static public function send (string $recipient, string $message)
  {
    global $config;
    switch ($config->textMessaging->provider) {

      case 'Twilio':
        return SMS::sendTwilio($recipient, $message);
        break;

      case 'ClickSend':
        return SMS::sendClickSend($recipient, $message);
        break;
    }
    return false;
  }


  static public function sendTwilio(string $recipient, string $message)
  {
    global $config;
    global $db;
    $tel = SMS::formattedPhoneNumber($recipient);
    // Only if the recipient has opted in to recieve messages
    if ($ok = $db->get_row("SELECT * FROM opt_in_text WHERE tel = :tel", ['tel' => $tel])) {
      $data = [
        'To' => $tel,
        'From' => $config->textMessaging->textFromNumber,
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
    }
    return false;
  }


  static public function sendClickSend(string $recipient, string $message)
  {
    global $db;
    $tel = SMS::formattedPhoneNumber($recipient);
    // Only if the recipient has opted in to recieve messages
    if ($ok = $db->get_row("SELECT * FROM opt_in_text WHERE tel = :tel", ['tel' => $tel])) {
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
    global $config;
    global $db;
    $phone = SMS::formattedPhoneNumber($recipient);
    $sql = "REPLACE INTO opt_in_text SET tel = :tel, opt_in = NOW()";
    $data = ['tel' => $phone];
    $result = $db->query($sql, $data);
    // Send a text confirmation
    $message = $config->textMessaging->optInConfirmationMessage;
    return SMS::send($phone, $message);
  }


  static public function optOut (string $recipient)
  {
    global $db;
    $tel = SMS::formattedPhoneNumber($recipient);
    $sql = "UPDATE opt_in_text SET opt_out = NOW() WHERE tel = :tel";
    $data = ['tel' => $tel];
    if (isset($config->textMessaging->optOutMessage)) {
      // Only if we have defined an opt out message
      SMS::send($tel, $config->textMessaging->optOutMessage);
    }
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