<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($input->getInt('id'));
$result = $user->setNewPassword($input->getRawString('password'));

exit(json_encode(['result' => $result]));
