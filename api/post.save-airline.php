<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Transport\User;
use Generic\InputHandler;

$user = new User($_SESSION['user']->id);

$uploadedFile = InputHandler::getFile('image', ['image/jpeg', 'image/png'], 5_000_000);
if ($uploadedFile)
{
  $savedFile = InputHandler::saveFile('image', '../images/airlines/');
  if (!$savedFile)
  {
    exit(json_encode(['result' => false, 'error' => 'Failed to save uploaded file.']));
  }
}

$airline = new Airline(InputHandler::getInt(INPUT_POST, 'id'));
$airline->name = InputHandler::getString(INPUT_POST, 'name');
$airline->flightNumberPrefix = InputHandler::getString(INPUT_POST, 'flightNumberPrefix');
if (isset($savedFile)) $airline->imageFilename = $savedFile;

if ($airline->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $airline->getId()]));
}

exit(json_encode(['result' => false, 'error' => $airline->getLastError()]));
