<?php
header('Content-Type: application/json');
require_once 'class.guest.php';
$json = json_decode(file_get_contents("php://input"));

$guest = new Guest($json->id);
$guest->firstName = $json->firstName ?: NULL;
$guest->lastName = $json->lastName ?: NULL;
$guest->emailAddress = $json->emailAddress ?: NULL;
$guest->phoneNumber = Utils::formattedPhoneNumber($json->phoneNumber);

if ($guest->save()) {
  $result = $guest->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
