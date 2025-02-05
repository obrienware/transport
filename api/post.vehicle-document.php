<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, VehicleDocument };
use Generic\InputHandler;

$user = new User($_SESSION['user']->id);

$uploadedFile = InputHandler::getFile('file', [], 5_000_000);
if (!$uploadedFile)
{
  exit(json_encode(['result' => false, 'error' => 'No file uploaded']));
}

$savedFile = InputHandler::saveFile('file', '../documents/');
if (!$savedFile)
{
  exit(json_encode(['result' => false, 'error' => 'Failed to save uploaded file.']));
}

$doc = new VehicleDocument();
$doc->vehicleId = InputHandler::getInt(INPUT_POST, 'vehicleId');
$doc->name = InputHandler::getString(INPUT_POST, 'documentName');
$doc->filename = $savedFile;
$doc->fileType = $uploadedFile['type'];

if ($doc->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $doc->getId()]));
}
exit(json_encode(['result' => false, 'error' => $doc->getLastError()]));
