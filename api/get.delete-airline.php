<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.airline.php';
$airline = new Airline($_REQUEST['id']);
$result = $airline->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));