<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Location, User };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$location = new Location($id);
if (!$location->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Location not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $location->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));