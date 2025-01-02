<?php
header('Content-Type: application/json');
require_once 'class.airline.php';
$airline = new Airline($_REQUEST['id']);
$result = $airline->delete();
die(json_encode(['result' => $result]));