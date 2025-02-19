<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ User, VehicleDocument };
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$doc = new VehicleDocument($id);
if (!$doc->getId()) {
  exit(json_encode([
    'result' => false,
    'error' => 'Document not found'
  ]));
}

$sessionUser = new User($_SESSION['user']->id);
$result = $doc->delete(userResponsibleForOperation: $sessionUser->getUsername());
exit(json_encode(['result' => $result]));