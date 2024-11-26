<?php
header('Content-Type: application/json');
require_once 'class.guest.php';
$json = json_decode(file_get_contents("php://input"));
$guest = new Guest($json->id);
$guest->firstName = $json->firstName ?: NULL;
$guest->lastName = $json->lastName ?: NULL;
$guest->emailAddress = $json->emailAddress ?: NULL;
$guest->phoneNumber = formattedPhoneNumber($json->phoneNumber);
$result = $guest->save();

echo json_encode(['result' => $result]);

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}