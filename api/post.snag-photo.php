<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, Snag };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$timestamp = (new DateTime('now', new DateTimeZone($_ENV['TZ'] ?? 'UTC')))->format('n/j g:i a');
$snagId = $input->getInt('id');
$photoId = $input->getInt('photoId');


$snag = new Snag($snagId);
$snag->attachImage($photoId);

if ($snag->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $snag->getId()]));
}
exit(json_encode(['result' => false, 'error' => $snag->getLastError()]));
