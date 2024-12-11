<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.event.php';
$event = new Event($_REQUEST['id']);
$before = $event->getState();
$description = 'Deleted event: '.$event->name;
Audit::log('deleted', 'events', $description, $before);
$result = $event->delete();
die(json_encode(['result' => $result]));