<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\AirportLocation;
use Transport\User;
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$id = InputHandler::getInt(INPUT_GET, 'id');

$airportLocation = new AirportLocation($id);
if (!$airportLocation->getId())
{
  exit(json_encode([
    'result' => false,
    'error' => 'Airport location not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airportLocation->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));
