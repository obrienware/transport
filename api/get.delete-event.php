<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.event.php';
$event = new Event($_REQUEST['id']);
$result = $event->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));