<?php
header('Content-Type: application/json');

require_once '../../autoload.php';

use Transport\User;

$json = json_decode(file_get_contents("php://input"));

$result = User::login($json->username, $json->password);
if ($result === false) exit(json_encode(false));
$_SESSION['user'] = $result;
exit(json_encode($result));
