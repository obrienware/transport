<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$query = "SELECT config, json5 FROM config WHERE node = :node";
$params = ['node' => $_REQUEST['node']];
$row = $db->get_row($query, $params);

echo json_encode([
  'json' => $row->config ?: '{}',
  'json5' => $row->json5 ?: '{}'
]);
