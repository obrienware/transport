<?php
@date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.config.php';
global $config;
$config = Config::get('organization');

require_once 'class.utils.php';
require_once 'class.data.php';

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
    $db = data::getInstance();
    $keys = Config::get('system')->keys;
    $tel = Utils::formattedPhoneNumber($recipient);
    // Only if the recipient has opted in to recieve messages
    if ($db->get_row("SELECT * FROM opt_in_text WHERE tel = :tel", ['tel' => $tel])) {
      $params = [
        'To' => $tel,
        'From' => $config->textMessaging->textFromNumber,
        'Body' => $message
      ];
      $result = Utils::callApi(
        'POST', 
        "https://api.twilio.com/2010-04-01/Accounts/{$_ENV['TWILIO_ACCOUNT_SID']}/Messages.json",
        $params, [
          'username' => $keys->TWILIO_ACCOUNT_SID,
          'password' => $keys->TWILIO_AUTH_TOKEN
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
    $db = data::getInstance();
    $keys = Config::get('system')->keys;
    $tel = Utils::formattedPhoneNumber($recipient);
    // Only if the recipient has opted in to recieve messages
    if ($db->get_row("SELECT * FROM opt_in_text WHERE tel = :tel", ['tel' => $tel])) {
      $messageObj = (object) ['messages' => [['body' => $message, 'to' => $tel]]];
      $data = json_encode($messageObj);
      $result = Utils::callApi('POST', 'https://rest.clicksend.com/v3/sms/send' , $data, [
        'username' => $keys->CLICKSEND_USERNAME,
        'password' => $keys->CLICKSEND_PASSWORD
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
    $db = data::getInstance();
    $phone = Utils::formattedPhoneNumber($recipient);
    $query = "REPLACE INTO opt_in_text SET tel = :tel, opt_in = NOW()";
    $params = ['tel' => $phone];
    $db->query($query, $params);
    // Send a text confirmation
    $message = $config->textMessaging->optInConfirmationMessage;
    return SMS::send($phone, $message);
  }


  static public function optOut (string $recipient)
  {
    global $config;
    $db = data::getInstance();
    $tel = Utils::formattedPhoneNumber($recipient);
    $query = "UPDATE opt_in_text SET opt_out = NOW() WHERE tel = :tel";
    $params = ['tel' => $tel];
    if (isset($config->textMessaging->optOutMessage)) {
      // Only if we have defined an opt out message
      SMS::send($tel, $config->textMessaging->optOutMessage);
    }
    return $db->query($query, $params);  
  }
}