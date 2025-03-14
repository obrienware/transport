<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\{ EmailTemplates, User };
use Generic\JsonInput;
use Generic\Logger;
Logger::logRequest();

$input = new JsonInput();

$user = new User($_SESSION['user']->id);

$template = new EmailTemplates($input->getInt('id'));
$template->content = $input->getRawString('content');

if ($template->save(userResponsibleForOperation: $user->getUsername()))
{
  exit(json_encode(['result' => $template->getId()]));
}
exit(json_encode(['result' => false, 'error' => $template->getLastError()]));
