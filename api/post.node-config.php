<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;

$json = json_decode(file_get_contents("php://input"));
$json->nodeConfig->updated = (object) [
  'date' => date('Y-m-d H:i:s'),
  'by' => $_SESSION['user']->username
];

$config = new Config($json->node);
$config->config = $json->nodeConfig;
$config->configString = $json->configString;
$config->save($_SESSION['user']->username);

echo json_encode(['result' => true]);