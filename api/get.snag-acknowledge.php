<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Snag, User };
use Generic\InputHandler;

$snagId = InputHandler::getInt(INPUT_GET, 'snagId');

$snag = new Snag($snagId);
if (!$snag->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Snag not found'
  ]));
}

$user = new User($_SESSION['user']->id);
$snag->acknowledged = 'now';
$snag->acknowledgedBy = $user->getUsername();
$result = $snag->save(userResponsibleForOperation: $user->getUsername());
exit(json_encode(['result' => $result]));