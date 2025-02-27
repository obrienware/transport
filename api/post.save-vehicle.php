<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Vehicle };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$vehicle = new Vehicle($input->getInt('id'));
$vehicle->color = $input->getString('color');
$vehicle->name = $input->getString('name');
$vehicle->description = $input->getString('description');
$vehicle->licensePlate = $input->getString('licensePlate');
$vehicle->passengers = $input->getInt('passengers');
$vehicle->requireCDL = $input->getBool('requireCDL');

if ($vehicle->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $vehicle->getId()]));
}
exit(json_encode(['result' => false, 'error' => $vehicle->getLastError()]));
