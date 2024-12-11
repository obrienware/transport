<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);
$before = $trip->getState();
$description = 'Deleted trip: '.$trip->summary;
Audit::log('deleted', 'trips', $description, $before);
$result = $trip->delete();
die(json_encode(['result' => $result]));