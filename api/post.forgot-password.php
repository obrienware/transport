<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Generic\JsonInput;

$input = new JsonInput();

$result = User::sendResetLink($input->getString('username'));

die(json_encode(['result' => $result]));
