<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));
require_once 'class.utils.php';
require_once 'class.user.php';
$user = new User();

$user->getUserByEmail($json->email);
$user->firstName = $json->firstName;
$user->lastName = $json->lastName;
$user->position = $json->position;
$user->phoneNumber = Utils::formattedPhoneNumber($json->phoneNumber);
$user->save();

$result = true;
die(json_encode(['result' => true]));