<?php
header('Content-Type: application/json');
require_once 'class.location.php';
$location = new Location($_REQUEST['id']);
$result = $location->delete();
die(json_encode(['result' => $result]));