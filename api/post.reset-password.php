<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$json = json_decode(file_get_contents("php://input"));
$user = new User($json->id);
$result = $user->setNewPassword($json->password);

exit(json_encode(['result' => $result]));