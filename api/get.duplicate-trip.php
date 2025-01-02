<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.trip.php';
$sourceTrip = new Trip($_REQUEST['id']);
$targetTrip = $sourceTrip->clone();
$targetTrip->save($user->getUsername());
$newId = $targetTrip->getId();
die(json_encode(['result' => $newId]));