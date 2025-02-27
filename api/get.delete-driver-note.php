<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\DriverNote;
use Transport\User;
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$id = InputHandler::getInt(INPUT_GET, 'id');

$note = new DriverNote($id);
if (!$note->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Note not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $note->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));