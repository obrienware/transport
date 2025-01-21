<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\AirportLocation;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$airportLocation = new AirportLocation($id);
$result = $airportLocation->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));