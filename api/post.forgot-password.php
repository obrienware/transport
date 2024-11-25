<?php
header('Content-Type: application/json');
require_once 'class.user.php';

$json = json_decode(file_get_contents("php://input"));

$result = User::sendResetLink($json->username);

die(json_encode(['result' => $result]));