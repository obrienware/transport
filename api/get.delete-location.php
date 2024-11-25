<?php
header('Content-Type: application/json');
require_once 'class.location.php';
$result = Location::deleteLocation($_REQUEST['id']);
die(json_encode(['result' => $result]));