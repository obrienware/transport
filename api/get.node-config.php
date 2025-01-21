<?php

use Transport\Database;

header('Content-Type: application/json');

require_once '../autoload.php';

$db = Database::getInstance();
$query = "SELECT config, json5 FROM config WHERE node = :node";
$params = ['node' => $_GET['node']];
$row = $db->get_row($query, $params);

echo json_encode([
  'json' => $row->config ?: '{}',
  'json5' => $row->json5 ?: '{}'
]);
