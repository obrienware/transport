<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$sessionUser = new User($_SESSION['user']->id);
$user = new User($_REQUEST['id']);
$result = $user->delete(userResponsibleForOperation: $sessionUser->getUsername());
die(json_encode(['result' => $result]));