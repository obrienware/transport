<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\DriverNote;
use Transport\User;
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$note = new DriverNote($input->getInt('id'));
$note->title = $input->getString('title');
$note->note = $input->getString('note');

if ($note->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $note->getId()]));
}
exit(json_encode(['result' => false, 'error' => $note->getLastError()]));
