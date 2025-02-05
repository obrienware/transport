<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$user = new User($id);
if (!$user->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'User not found'
  ]));
}

$sessionUser = new User($_SESSION['user']->id);
$result = $user->delete(userResponsibleForOperation: $sessionUser->getUsername());
exit(json_encode(['result' => $result]));