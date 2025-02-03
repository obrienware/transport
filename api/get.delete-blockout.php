<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Blockout;
use Transport\User;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$blockout = new Blockout($id);
if (!$blockout->getId()) {
  die(json_encode([
    'result' => false,
    'error' => 'Blockout not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$result = $blockout->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));