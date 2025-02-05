<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Generic\JsonInput;

$input = new JsonInput();

$nodeConfig = $input->getObject('nodeConfig');
$nodeConfig->updated = (object) [
  'date' => date('Y-m-d H:i:s'),
  'by' => $_SESSION['user']->username
];

$config = new Config($input->getString('node'));
$config->config = $nodeConfig;
$config->configString = $input->getRawString('configString');
$config->save($_SESSION['user']->username);

echo json_encode(['result' => true]);
