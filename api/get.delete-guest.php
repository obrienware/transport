<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Guest, User };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$guest = new Guest($id);
if (!$guest->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Guest not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $guest->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));