<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
$sourceTrip = new Trip($_REQUEST['id']);
$targetTrip = $sourceTrip->clone();
$targetTrip->save();
$newId = $targetTrip->getId();
die(json_encode(['result' => $newId]));