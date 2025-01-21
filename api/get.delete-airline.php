<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$airline = new Airline($id);
$result = $airline->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));