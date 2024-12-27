<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.airline.php';
$airline = new Airline($_REQUEST['id']);
$before = $airline->getState();
$description = 'Deleted airline: '.$airline->name;
Audit::log('deleted', 'airlines', $description, $before);
$result = $airline->delete();
die(json_encode(['result' => $result]));