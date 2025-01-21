<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Blockout;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$blockout = new Blockout($json->id);
if (hasValue($json->userId)) $blockout->userId = $json->userId;
$blockout->fromDateTime = parseValue($json->fromDateTime);
$blockout->toDateTime = parseValue($json->toDateTime);
$blockout->note = parseValue($json->note);

function hasValue($value) {
    return isset($value) && $value !== '';
}

function parseValue($value) {
    return hasValue($value) ? $value : NULL;
}

if ($blockout->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $blockout->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
