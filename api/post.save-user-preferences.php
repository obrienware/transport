<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$json = json_decode(file_get_contents("php://input"));

$user = new User($_SESSION['user']->id);
$user->preferences = $json;


if ($user->save($user->getUsername())) {
  $result = $user->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));