<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Trip;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

$sourceTrip = new Trip($id);
if (!$sourceTrip->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Trip not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$targetTrip = $sourceTrip->clone();
$targetTrip->save(userResponsibleForOperation: $user->getUsername());
$newId = $targetTrip->getId();
die(json_encode(['result' => $newId]));