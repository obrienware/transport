<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();
$q = '%' . $_GET['query'] . '%';
$query = "
  SELECT 
    id AS value,
    CONCAT(first_name, ' ', last_name) AS label
  FROM users 
  WHERE 
    CONCAT(first_name,' ',last_name) LIKE :query
    AND FIND_IN_SET('requestor', roles)
    AND archived IS NULL
  ORDER BY first_name, last_name
";
$params = ['query' => $q];
if ($rows = $db->get_rows($query, $params)) {
  die(json_encode($rows));
}
echo '[]';