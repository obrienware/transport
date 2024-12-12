<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.guest.php';
$json = json_decode(file_get_contents("php://input"));

$guest = new Guest($json->id);
$previousName = $guest->getName();

$guest->firstName = $json->firstName ?: NULL;
$guest->lastName = $json->lastName ?: NULL;
$guest->emailAddress = $json->emailAddress ?: NULL;
$guest->phoneNumber = formattedPhoneNumber($json->phoneNumber);

$result = $guest->save();
if ($json->id) {
  $before = $guest->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed guest: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added guest: '.$json->firstName.' '.$json->lastName;
}
$guest->getGuest($id);
$after = $guest->getState();
Audit::log($action, 'guests', $description, $before, $after);

echo json_encode(['result' => $result]);

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}