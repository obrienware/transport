<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\EmailTemplates;
use Transport\User;
use Generic\JsonInput;

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$template = new EmailTemplates($input->getInt('id'));
$template->content = $input->getRawString('content');

if ($template->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $template->getId()]));
}
exit(json_encode(['result' => false, 'error' => $template->getLastError()]));
