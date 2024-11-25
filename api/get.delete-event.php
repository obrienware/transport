<?php
header('Content-Type: application/json');
require_once 'class.event.php';
$result = Event::deleteEvent($_REQUEST['id']);
die(json_encode(['result' => $result]));