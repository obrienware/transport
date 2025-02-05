<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airport;
use Transport\User;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$airport = new Airport($id);
if (!$airport->getId())
{
  exit(json_encode([
    'result' => false,
    'error' => 'Airport not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airport->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));
