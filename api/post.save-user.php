<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.user.php';
$json = json_decode(file_get_contents("php://input"));

$user = new User($json->id);
$previousName = $user->getName();

$user->username = $json->username ?: NULL;
$user->firstName = $json->firstName ?: NULL;
$user->lastName = $json->lastName ?: NULL;
$user->emailAddress = $json->emailAddress ?: NULL;
$user->phoneNumber = formattedPhoneNumber($json->phoneNumber) ?: NULL;
$user->roles = $json->roles ? explode(',', $json->roles) : NULL;
$user->position = $json->position ?: NULL;
$user->departmentId = $json->departmentId ?: NULL;
$user->CDL = $json->cdl ?: NULL;

$result = $user->save();
if ($json->id) {
  $before = $user->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed user: '.$previousName;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added user: '.$json->firstName.' '.$json->lastName;
}
$user->getUser($id);
$after = $user->getState();
Audit::log($action, 'users', $description, $before, $after);

if ($json->resetPassword) {
	$user->resetPassword();
}

echo json_encode(['result' => $result]);

function formattedPhoneNumber($number) 
{
  if (str_contains($number, '+')) {
    return $number;
  } else {
    return preg_replace('~.*(\d{3})[^\d]{0,7}(\d{3})[^\d]{0,7}(\d{4}).*~', '($1) $2-$3', $number);
  }
}