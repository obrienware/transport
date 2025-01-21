<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Department;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$department = new Department($id);
$result = $department->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));