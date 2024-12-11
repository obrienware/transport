<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.user.php';
$user = new User($_REQUEST['id']);
$before = $user->getState();
$description = 'Deleted user: '.$user->getName();
Audit::log('deleted', 'users', $description, $before);
$result = $user->delete();
die(json_encode(['result' => $result]));