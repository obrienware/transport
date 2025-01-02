<?php
header('Content-Type: application/json');
require_once 'class.airport.php';
$airport = new Airport($_REQUEST['id']);
$result = $airport->delete();
die(json_encode(['result' => $result]));