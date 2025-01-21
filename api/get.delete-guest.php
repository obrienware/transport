<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Guest;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$guest = new Guest($id);
$result = $guest->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));