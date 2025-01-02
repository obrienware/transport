<?php
header('Content-Type: application/json');
require_once 'class.blockout.php';
$blockout = new Blockout($_REQUEST['id']);
$result = $blockout->delete();
die(json_encode(['result' => $result]));