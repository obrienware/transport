<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$query = '%' . $_GET['query'] . '%';
$sql = "
  SELECT 
    id AS value,
    `name` AS label,
    short_name,
    type
  FROM locations WHERE `name` LIKE :query OR short_name LIKE :query
  ORDER BY `name`
";
$data = ['query' => $query];
if ($rs = $db->get_results($sql, $data)) {
  die(json_encode($rs));
}
echo '[]';