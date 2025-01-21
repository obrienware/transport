<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Utils;

$json = json_decode(file_get_contents("php://input"));

$sessionUser = new User($_SESSION['user']->id);

$user = new User($json->id);
$user->username = parseValue($json->username);
$user->firstName = parseValue($json->firstName);
$user->lastName = parseValue($json->lastName);
$user->emailAddress = parseValue($json->emailAddress);
$user->phoneNumber = hasValue($json->phoneNumber) ? Utils::formattedPhoneNumber($json->phoneNumber) : NULL;
$user->roles = hasValue($json->roles) ? explode(',', $json->roles) : [];
$user->position = parseValue($json->position);
$user->departmentId = hasValue($json->departmentId) ? (int)$json->departmentId : NULL;
$user->CDL = hasValue($json->cdl) ? (bool)$json->cdl : false;

function hasValue($value) {
    return isset($value) && $value !== '';
}

function parseValue($value) {
    return hasValue($value) ? $value : NULL;
}

if ($user->save(userResponsibleForOperation: $sessionUser->getUsername())) {
    if ($json->resetPassword) {
        $user->resetPassword();
    }
    $result = $user->getId();
    die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));