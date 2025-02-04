<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airport;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);

$airport = new Airport($id);
if (!$airport->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Airport not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airport->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));