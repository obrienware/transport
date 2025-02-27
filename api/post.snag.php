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

$snag = new Snag($input->getInt('id'));
$snag->vehicleId = $input->getInt('vehicleId');
$snag->userId = $user->getId();
$snag->description = $input->getString('description');
$snag->logged = 'now';

if ($snag->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $snag->getId()]));
}
exit(json_encode(['result' => false, 'error' => $snag->getLastError()]));
