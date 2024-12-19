<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.user.php';
$json = json_decode(file_get_contents("php://input"));

$user = new User($_SESSION['user']->id);
$before = $user->getState();
$user->preferences = $json;
$result = $user->save();
$user->getUser($_SESSION['user']->id);
$after = $user->getState();

Audit::log('modified', 'users', 'User '.$user->getName().' updated their personal preferences', $before, $after);

echo json_encode(['result' => $result]);