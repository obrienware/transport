<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$result = User::deleteUser($_REQUEST['id']);
die(json_encode(['result' => $result]));