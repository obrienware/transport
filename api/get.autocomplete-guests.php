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
    CONCAT(first_name, ' ', last_name) AS label
  FROM guests 
  WHERE 
    CONCAT(first_name,' ',last_name) LIKE :query
    AND archived IS NULL
  ORDER BY first_name, last_name
";
$params = ['query' => $q];
if ($rows = $db->get_rows($query, $params)) {
  die(json_encode($rows));
}
echo '[]';