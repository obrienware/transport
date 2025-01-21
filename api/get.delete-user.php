<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$sessionUser = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$user = new User($id);
$result = $user->delete(userResponsibleForOperation: $sessionUser->getUsername());
die(json_encode(['result' => $result]));