<?php
header('Content-Type: application/json');
require_once 'class.blockout.php';
$json = json_decode(file_get_contents("php://input"));

$blockout = new Blockout($json->id);
$blockout->userId = $json->userId ?: NULL;
$blockout->fromDateTime = $json->fromDateTime ?: NULL;
$blockout->toDateTime = $json->toDateTime ?: NULL;
$blockout->note = $json->note ?: NULL;

$result = $blockout->save();

echo json_encode([
  'result' => $result
]);
