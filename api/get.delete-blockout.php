<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.blockout.php';
$blockout = new Blockout($_REQUEST['id']);

$before = $blockout->getState();
$description = 'Deleted blockout date(s)';
Audit::log('deleted', 'user_blockout_dates', $description, $before);
$result = $blockout->delete();

die(json_encode(['result' => $result]));