<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\AirportLocation;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) $id = (int)$id;

$airportLocation = new AirportLocation($id);
if (!$airportLocation->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Airport location not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $airportLocation->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));