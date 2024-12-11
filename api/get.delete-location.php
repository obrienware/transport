<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.location.php';
$location = new Location($_REQUEST['id']);
$before = $location->getState();
$description = 'Deleted location: '.$location->name;
Audit::log('deleted', 'locations', $description, $before);
$result = $location->delete();
die(json_encode(['result' => $result]));