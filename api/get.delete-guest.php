<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.guest.php';
$guest = new Guest($_REQUEST['id']);
$before = $guest->getState();
$description = 'Deleted guest: '.$guest->getName();
Audit::log('deleted', 'guests', $description, $before);
$result = $guest->delete();
die(json_encode(['result' => $result]));