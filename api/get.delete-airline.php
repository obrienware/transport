<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) $id = (int)$id;

$airline = new Airline($id);
if (!$airline->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Airline not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airline->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));