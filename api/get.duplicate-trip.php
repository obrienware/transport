<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Trip;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$sourceTrip = new Trip($id);
$targetTrip = $sourceTrip->clone();
$targetTrip->save(userResponsibleForOperation: $user->getUsername());
$newId = $targetTrip->getId();
die(json_encode(['result' => $newId]));