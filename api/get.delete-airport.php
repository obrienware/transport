<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airport;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$airport = new Airport($id);
$result = $airport->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));