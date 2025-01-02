<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.vehicle.php';
$vehicle = new Vehicle($_REQUEST['id']);
$result = $vehicle->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));