<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;
use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) $id = (int)$id;

$vehicle = new Vehicle($id);
if (!$vehicle->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Vehicle not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $vehicle->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));