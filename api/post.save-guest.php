<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Guest;
use Transport\User;
use Transport\Utils;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$guest = new Guest($json->id);
$guest->firstName = parseValue($json->firstName);
$guest->lastName = parseValue($json->lastName);
$guest->emailAddress = parseValue($json->emailAddress);
$guest->phoneNumber = hasValue($json->phoneNumber) ? Utils::formattedPhoneNumber($json->phoneNumber) : NULL;

function hasValue($value) {
    return isset($value) && $value !== '';
}

function parseValue($value) {
    return hasValue($value) ? $value : NULL;
}

if ($guest->save(userResponsibleForOperation: $user->getUsername())) {
    $result = $guest->getId();
    die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
