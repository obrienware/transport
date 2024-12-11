<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.user.php';
$user = new User();

$user->getUserByEmail($json->email);
$user->firstName = $json->firstName;
$user->lastName = $json->lastName;
$user->position = $json->position;
$user->phoneNumber = formattedPhoneNumber($json->phoneNumber);
$user->save();

$result = true;
die(json_encode(['result' => true]));

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}