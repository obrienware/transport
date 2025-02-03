<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\EmailTemplates;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$template = new EmailTemplates($json->id);
$template->content = $json->content;

if ($template->save(userResponsibleForOperation: $user->getUsername())) {
  exit(json_encode(['result' => $template->getId()]));
}
exit(json_encode(['result' => false, 'error' => $template->getLastError()]));
