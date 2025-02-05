<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Vehicle };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$vehicle = new Vehicle($id);
if (!$vehicle->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Vehicle not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $vehicle->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));