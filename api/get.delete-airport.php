<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.airport.php';
$airport = new Airport($_REQUEST['id']);
$result = $airport->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));