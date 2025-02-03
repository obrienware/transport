<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;

$db = Database::getInstance();

$q = '%'.filter_input(INPUT_GET, 'query', FILTER_SANITIZE_FULL_SPECIAL_CHARS).'%';
$query = "
  SELECT 
    id AS value,
    `name` AS label,
    short_name,
    type
  FROM locations WHERE `name` LIKE :query OR short_name LIKE :query
  ORDER BY `name`
";
$params = ['query' => $q];
if ($rows = $db->get_rows($query, $params)) {
  die(json_encode($rows));
}
echo '[]';