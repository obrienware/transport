<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$json = json_decode(file_get_contents("php://input"));

$user = new User($_SESSION['user']->id);
$user->preferences = $json;


if ($user->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $user->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));