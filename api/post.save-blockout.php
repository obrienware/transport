<?php
header('Content-Type: application/json');
require_once 'class.audit.php';
require_once 'class.blockout.php';
$json = json_decode(file_get_contents("php://input"));

$blockout = new Blockout($json->id);
$previousNote = $blockout->note;

$blockout->userId = $json->userId ?: NULL;
$blockout->fromDateTime = $json->fromDateTime ?: NULL;
$blockout->toDateTime = $json->toDateTime ?: NULL;
$blockout->note = $json->note ?: NULL;

$result = $blockout->save();
if ($json->id) {
  $before = $blockout->getState();
  $id = $json->id;
  $action = 'modified';
  $description = 'Changed block out period: '.$previousNote;
} else {
  $id = $result['result'];
  $action = 'added';
  $description = 'Added block out period: '.$json->note;
}
$blockout->getBlockout($id);
$after = $blockout->getState();
Audit::log($action, 'user_blockout_dates', $description, $before, $after);

echo json_encode(['result' => $result]);
