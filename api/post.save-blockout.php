<?php
declare(strict_types=1);

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
  exit(json_encode(['result' => $blockout->getId()]));
}
exit(json_encode(['result' => false, 'error' => $blockout->getLastError()]));
