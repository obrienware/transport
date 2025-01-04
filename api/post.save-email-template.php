<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);

require_once 'class.email-templates.php';
$json = json_decode(file_get_contents("php://input"));

$template = new EmailTemplates($json->id);
$template->content = $json->content;

if ($template->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $template->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
