<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.blockout.php';
$blockout = new Blockout($_REQUEST['id']);
$result = $blockout->delete($user->getUsername());
die(json_encode(['result' => $result]));