<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Airport, User };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$airport = new Airport($input->getInt('id'));
$airport->IATA = $input->getString('iata');
$airport->name = $input->getString('name');
$airport->stagingLocationId = $input->getInt('stagingLocationId');
$airport->leadTime = $input->getInt('leadTime');
$airport->travelTime = $input->getInt('travelTime');
$airport->arrivalInstructions = $input->getString('arrivalInstructions');
$airport->arrivalInstructionsGroup = $input->getString('arrivalInstructionsGroup');

if ($airport->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $airport->getId()]));
}
exit(json_encode(['result' => false, 'error' => $airport->getLastError()]));
