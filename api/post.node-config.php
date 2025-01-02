<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$json = json_decode(file_get_contents("php://input"));
$json->nodeConfig->updated = (object) [
  'date' => date('Y-m-d H:i:s'),
  'by' => $_SESSION['user']->username
];

$query = "UPDATE config SET config = :config, json5 = :json5 WHERE node = :node";
$params = [
  'config' => json_encode($json->nodeConfig, JSON_PRETTY_PRINT),
  'json5' => $json->configString,
  'node' => $json->node
];
$db->query($query, $params);

echo json_encode(['result' => true, 'meta' => $db->errorInfo]);

$query = "
  INSERT INTO config_log SET
    datetimestamp = NOW(),
    node = :node,
    config = :config,
    user = :user
";
$params = [
  'node' => $json->node,
  'config' => $json->configString,
  'user' => $_SESSION['user']->username
];
$db->query($query, $params);