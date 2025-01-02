<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.airport-location.php';
$airportLocation = new AirportLocation($_REQUEST['id']);
$result = $airportLocation->delete($user->getUsername());
die(json_encode(['result' => $result]));