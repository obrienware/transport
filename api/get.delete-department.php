<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Department;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) $id = (int)$id;

$department = new Department($id);
if (!$department->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Department not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $department->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));