<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Blockout, User };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$userId = $input->getInt('userId');
$blockout = new Blockout($input->getInt('id'));
if ($userId) $blockout->userId = $userId;
$blockout->fromDateTime = $input->getString('fromDateTime');
$blockout->toDateTime = $input->getString('toDateTime');
$blockout->note = $input->getString('note');

if ($blockout->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $blockout->getId()]));
}
exit(json_encode(['result' => false, 'error' => $blockout->getLastError()]));
