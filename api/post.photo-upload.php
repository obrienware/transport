<?php
declare(strict_types=1);

header("Content-Type: application/json");

require_once '../autoload.php';

use Transport\{ User, ImageLibrary };
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$user = new User($_SESSION['user']->id);

$uploadedFile = InputHandler::getFile('file', [], 5_000_000);
if (!$uploadedFile)
{
  exit(json_encode(['result' => false, 'error' => 'No file uploaded']));
}

$savedFile = InputHandler::saveFile('file', __DIR__.'/../images/library/', 'jpg'); // Uploaded this way, the image will have been converted and uploaded as a jpg image even if the original extension was something else (like an .svg).
if (!$savedFile)
{
  exit(json_encode(['result' => false, 'error' => 'Failed to save uploaded file.']));
}

$image = new ImageLibrary();
$image->title = InputHandler::getString(INPUT_POST, 'description');
$image->fileName = $savedFile;
$image->fileType = $uploadedFile['type'];

if ($image->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $image->getId()]));
}
exit(json_encode(['result' => false, 'error' => $image->getLastError()]));
