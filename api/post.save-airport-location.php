<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\AirportLocation;
use Transport\User;
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$airportLocation = new AirportLocation($input->getInt('id'));
$airportLocation->airportId = $input->getInt('airportId');
$airportLocation->airlineId = $input->getInt('airlineId');
$airportLocation->locationId = $input->getInt('locationId');
$airportLocation->type = $input->getString('type');

if ($airportLocation->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $airportLocation->getId()]));
}
exit(json_encode(['result' => false, 'error' => $airportLocation->getLastError()]));
