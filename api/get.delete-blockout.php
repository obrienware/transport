<?php
header('Content-Type: application/json');
require_once 'class.blockout.php';
$result = Blockout::deleteBlockout($_REQUEST['id']);
die(json_encode(['result' => $result]));