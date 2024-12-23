<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.airport.php';
$airport = new Airport($_REQUEST['id']);
$before = $airport->getState();
$description = 'Deleted airport: '.$airport->name;
Audit::log('deleted', 'airports', $description, $before);
$result = $airport->delete();
die(json_encode(['result' => $result]));