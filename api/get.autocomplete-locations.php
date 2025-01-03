<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$q = '%' . $_GET['query'] . '%';
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