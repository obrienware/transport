<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Guest;
use Transport\User;
use Transport\Utils;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$phoneNumber = hasValue($json->phoneNumber) ? Utils::formattedPhoneNumber($json->phoneNumber) : null;

$guest = new Guest($json->id);
if (!isset($json->id)) {
  $guest->getGuestByPhoneNumber($phoneNumber); // This will ensure that we don't create duplicate guests with the same phone number
}
$guest->firstName = parseValue($json->firstName);
$guest->lastName = parseValue($json->lastName);
$guest->emailAddress = parseValue($json->emailAddress);
$guest->phoneNumber = $phoneNumber;

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : null;
}

if ($guest->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $guest->getId()]));
}
exit(json_encode(['result' => false, 'error' => $guest->getLastError()]));
