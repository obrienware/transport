<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Snag };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$timestamp = (new DateTime('now', new DateTimeZone($_ENV['TZ'] ?? 'UTC')))->format('n/j g:i a');
$snag = new Snag($input->getInt('id'));
$snag->resolved = 'now';
$snag->resolution = $input->getString('text');
$snag->resolvedBy = $user->getUsername();

if ($snag->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $snag->getId()]));
}
exit(json_encode(['result' => false, 'error' => $snag->getLastError()]));
