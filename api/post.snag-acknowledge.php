<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Snag };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$snag = new Snag($input->getInt('id'));
$snag->acknowledgedBy = $user->getUsername();
$snag->acknowledged = 'now';

if ($snag->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $snag->getId()]));
}
exit(json_encode(['result' => false, 'error' => $snag->getLastError()]));
