<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;

$user = new User($id);
if (!$user->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'User not found'
  ]));
}

$sessionUser = new User($_SESSION['user']->id);
$result = $user->delete(userResponsibleForOperation: $sessionUser->getUsername());
die(json_encode(['result' => $result]));