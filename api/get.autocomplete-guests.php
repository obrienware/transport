<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$query = '%' . $_GET['query'] . '%';
$sql = "
  SELECT 
    id AS value,
    CASE WHEN group_name IS NOT NULL THEN
      group_name
    ELSE
      CONCAT(first_name, ' ', last_name) 
    END AS label
  FROM guests 
  WHERE 
    (
      CONCAT(first_name,' ',last_name) LIKE :query
      OR group_name LIKE :query
    )
    AND archived IS NULL
  ORDER BY first_name, last_name, group_name
";
$data = ['query' => $query];
if ($rs = $db->get_results($sql, $data)) {
  die(json_encode($rs));
}
echo '[]';