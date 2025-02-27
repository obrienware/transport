<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Snag };
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$id = InputHandler::getInt(INPUT_GET, 'id');

$snag = new Snag($id);
if (!$snag->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Snag not found'
  ]));
}

$sessionUser = new User($_SESSION['user']->id);
$result = $snag->delete(userResponsibleForOperation: $sessionUser->getUsername());
exit(json_encode(['result' => $result]));