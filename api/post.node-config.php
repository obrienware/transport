<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$json = json_decode(file_get_contents("php://input"));
$json->nodeConfig->updated = (object) [
  'date' => date('Y-m-d H:i:s'),
  'by' => $_SESSION['user']->username
];

$sql = "UPDATE config SET config = :config, json5 = :json5 WHERE node = :node";
$data = [
  'config' => json_encode($json->nodeConfig, JSON_PRETTY_PRINT),
  'json5' => $json->configString,
  'node' => $json->node
];
$db->query($sql, $data);

echo json_encode(['result' => true, 'meta' => $db->errorInfo]);

$sql = "
  INSERT INTO config_log SET
    datetimestamp = NOW(),
    node = :node,
    config = :config,
    user = :user
";
$data = [
  'node' => $json->node,
  'config' => $json->configString,
  'user' => $_SESSION['user']->username
];
$db->query($sql, $data);