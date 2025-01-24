<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\DriverNote;
use Transport\User;

$user = new User($_SESSION['user']->id);

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$note = new DriverNote($id);
$result = $note->delete(userResponsibleForOperation: $user->getUsername());
die(json_encode(['result' => $result]));