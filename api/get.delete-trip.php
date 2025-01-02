<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);
$result = $trip->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));