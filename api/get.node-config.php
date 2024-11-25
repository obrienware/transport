<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$sql = "SELECT config, json5 FROM config WHERE node = :node";
$data = ['node' => $_REQUEST['node']];
$row = $db->get_row($sql, $data);

echo json_encode([
  'json' => $row->config ?: '{}',
  'json5' => $row->json5 ?: '{}'
]);
