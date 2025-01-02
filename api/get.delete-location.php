<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.location.php';
$location = new Location($_REQUEST['id']);
$result = $location->delete($user->getUsername());
die(json_encode(['result' => $result]));