<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$json = json_decode(file_get_contents("php://input"));
$user = new User($json->id);
$result = $user->setNewPassword($json->password);
die(json_encode(['result' => $result]));