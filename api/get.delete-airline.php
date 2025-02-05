<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\User;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$airline = new Airline($id);
if (!$airline->getId())
{
  exit(json_encode([
    'result' => false,
    'error' => 'Airline not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airline->delete(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));
