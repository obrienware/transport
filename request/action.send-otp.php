<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Email;

$user = new User();
$user->getUserByEmail($_GET['email']);
if (!$user->getId()) {
  $user->roles = ['requestor'];
  $user->save(); // So we can have a valid user object
}

$otp = $user->setPasswordToken();


$email = new Email();
$email->setSubject('One time passcode');
$email->setContent("
Your one-time passcode for submitting a transportation request is: {$otp}
");
$email->addRecipient($user->emailAddress, null);
$result = $email->sendText();
echo json_encode(['result' => $result]);