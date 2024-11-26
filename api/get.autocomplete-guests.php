<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$query = '%' . $_GET['query'] . '%';
$sql = "
  SELECT 
    id AS value,
    CONCAT(first_name, ' ', last_name) AS label
  FROM guests 
  WHERE 
    CONCAT(first_name,' ',last_name) LIKE :query
    AND archived IS NULL
  ORDER BY first_name, last_name
";
$data = ['query' => $query];
if ($rs = $db->get_results($sql, $data)) {
  die(json_encode($rs));
}
echo '[]';