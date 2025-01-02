<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.guest.php';
$guest = new Guest($_REQUEST['id']);
$result = $guest->delete($user->getUsername());
die(json_encode(['result' => $result]));