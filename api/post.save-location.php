<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Location, User };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$location = new Location($input->getInt('id'));
$location->name = $input->getString('name');
$location->shortName = $input->getString('shortName');
$location->description = $input->getString('description');
$location->type = $input->getString('type');
$location->IATA = $input->getString('IATA');
$location->mapAddress = $input->getString('mapAddress');
$location->lat = $input->getFloat('lat');
$location->lon = $input->getFloat('lon');
$location->placeId = $input->getString('placeId');
$location->meta = $input->getRawString('meta');

if ($location->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $location->getId()]));
}
exit(json_encode(['result' => false, 'error' => $location->getLastError()]));
