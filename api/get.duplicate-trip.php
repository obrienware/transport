<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Trip, User };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$sourceTrip = new Trip($id);
if (!$sourceTrip->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Trip not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$targetTrip = $sourceTrip->clone();
$targetTrip->save(userResponsibleForOperation: $user->getUsername());
$newId = $targetTrip->getId();
exit(json_encode(['result' => $newId]));