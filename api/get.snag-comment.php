<?php
header('Content-Type: application/json');
require_once '../autoload.php';

use Transport\Snag;
use Transport\User;

$snagId = isset($_GET['snagId']) ? (int)$_GET['snagId'] : null;
$snag = new Snag($snagId);

$user = new User($_SESSION['user']->id);

$snag->comments = $_GET['text'];

if ($snag->save(userResponsibleForOperation: $user->getUsername())) {
  $result = $snag->getId();
  die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
