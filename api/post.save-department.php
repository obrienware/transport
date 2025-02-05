<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ Department, User };
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$department = new Department($input->getInt('id'));
$department->name = $input->getString('name');
$department->mayRequest = $input->getBool('mayRequest');

if ($department->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $department->getId()]));
}
exit(json_encode(['result' => false, 'error' => $department->getLastError()]));
