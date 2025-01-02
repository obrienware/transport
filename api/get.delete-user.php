<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_REQUEST['id']);
$result = $user->delete();
die(json_encode(['result' => $result]));