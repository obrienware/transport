<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Blockout;
use Transport\User;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$blockout = new Blockout($id);
if (!$blockout->getId())
{
  die(json_encode([
    'result' => false,
    'error' => 'Blockout not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $blockout->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));
