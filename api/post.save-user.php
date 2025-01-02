<?php
header('Content-Type: application/json');
require_once 'class.user.php';
require_once 'class.utils.php';
$json = json_decode(file_get_contents("php://input"));

$sessionUser = new User($_SESSION['user']->id);

$user = new User($json->id);
$user->username = $json->username ?: NULL;
$user->firstName = $json->firstName ?: NULL;
$user->lastName = $json->lastName ?: NULL;
$user->emailAddress = $json->emailAddress ?: NULL;
$user->phoneNumber = Utils::formattedPhoneNumber($json->phoneNumber) ?: NULL;
$user->roles = $json->roles ? explode(',', $json->roles) : NULL;
$user->position = $json->position ?: NULL;
$user->departmentId = $json->departmentId ?: NULL;
$user->CDL = $json->cdl ?: NULL;

if ($user->save($sessionUser->getUsername())) {
	if ($json->resetPassword) {
		$user->resetPassword();
	}
	$result = $user->getId();
	die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));