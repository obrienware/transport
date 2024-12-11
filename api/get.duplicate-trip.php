<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.trip.php';

$sourceTrip = new Trip($_REQUEST['id']);
$before = $sourceTrip->getState();

$targetTrip = clone $sourceTrip;
unset($targetTrip->tripId); // To force it to save as a new database record
$targetTrip->summary = $targetTrip->summary.' (copy)';
$resp = $targetTrip->save();
$newId = $resp['result'];
$targetTrip->getTrip($newId);
$after = $targetTrip->getState();

$description = 'Duplicated trip: '.$sourceTrip->summary;

Audit::log('added', 'trips', $description, $before, $after);
die(json_encode(['result' => $newId]));