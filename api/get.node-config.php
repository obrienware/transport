<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;
use Generic\InputHandler;

$node = InputHandler::getString(INPUT_GET, 'node');

$db = Database::getInstance();
$query = "SELECT config, json5 FROM config WHERE node = :node";
$params = ['node' => $node];
if ($row = $db->get_row($query, $params)) {
  die(json_encode([
    'json' => $row->config ?? '{}',
    'json5' => $row->json5 ?? '{}'
  ]));
}

echo 'false';
