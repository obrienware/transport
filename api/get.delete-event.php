<?php
header('Content-Type: application/json');
require_once 'class.event.php';
$event = new Event($_REQUEST['id']);
$result = $event->delete();
die(json_encode(['result' => $result]));