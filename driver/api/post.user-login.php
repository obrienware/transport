<?php
header('Content-Type: application/json');
$json = json_decode(file_get_contents("php://input"));

require_once 'class.user.php';
$result = User::login($json->username, $json->password);
if ($result === false) die(json_encode(false));
$_SESSION['user'] = $result;
die(json_encode($result));
