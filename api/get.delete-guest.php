<?php
header('Content-Type: application/json');
require_once 'class.guest.php';
$result = Guest::deleteGuest($_REQUEST['id']);
die(json_encode(['result' => $result]));