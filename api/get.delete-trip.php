<?php
header('Content-Type: application/json');
require_once 'class.trip.php';
$result = Trip::deleteTrip($_REQUEST['id']);
die(json_encode(['result' => $result]));