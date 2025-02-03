<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Department;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$department = new Department($json->id);
$department->name = parseValue($json->name);
$department->mayRequest = parseValue($json->mayRequest);

function hasValue($value) {
  return isset($value) && $value !== '';
}

function parseValue($value) {
  return hasValue($value) ? $value : NULL;
}

if ($department->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $department->getId()]));
}
exit(json_encode(['result' => false, 'error' => $department->getLastError()]));
