<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Generic\Utils;

$json = json_decode(file_get_contents("php://input"));

$user = new User();

$user->getUserByEmail($json->email);
$user->firstName = $json->firstName;
$user->lastName = $json->lastName;
$user->position = $json->position;
$user->phoneNumber = Utils::formattedPhoneNumber($json->phoneNumber);
$user->save();

$result = true;
exit(json_encode(['result' => true]));