<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.blockout.php';
$json = json_decode(file_get_contents("php://input"));

$blockout = new Blockout($json->id);
if ($json->userId) $blockout->userId = $json->userId;
$blockout->fromDateTime = $json->fromDateTime ?: NULL;
$blockout->toDateTime = $json->toDateTime ?: NULL;
$blockout->note = $json->note ?: NULL;

if ($blockout->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $blockout->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
