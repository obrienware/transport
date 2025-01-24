<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\DriverNote;
use Transport\User;

$user = new User($_SESSION['user']->id);

$json = json_decode(file_get_contents("php://input"));

$note = new DriverNote($json->id);
$note->title = parseValue($json->title);
$note->note = parseValue($json->note);

function hasValue($value) {
    return isset($value) && $value !== '';
}

function parseValue($value) {
    return hasValue($value) ? $value : NULL;
}

if ($note->save(userResponsibleForOperation: $user->getUsername())) {
    $result = $note->getId();
    die(json_encode(['result' => $result]));
}
die(json_encode(['result' => false]));
