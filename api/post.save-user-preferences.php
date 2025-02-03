<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$json = json_decode(file_get_contents("php://input"));

$user = new User($_SESSION['user']->id);
$user->preferences = $json;


if ($user->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $user->getId()]));
}
exit(json_encode(['result' => false, 'error' => $user->getLastError()]));