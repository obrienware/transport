<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$vehicle = new Vehicle($id);
$result = $vehicle->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));