<?php
header('Content-Type: application/json');
require_once 'class.guest.php';
$guest = new Guest($_REQUEST['id']);
$result = $guest->delete();
die(json_encode(['result' => $result]));