<?php
require_once 'class.data.php';
if (!$db) $db = new data();

// Is there a trip in progress?
$sql = "
  SELECT * FROM trips 
  WHERE 
    driver_id = :id
    AND end_date >= CURDATE()
    AND archived IS NULL
    AND started IS NOT NULL
    AND completed IS NULL
";
$data = ['id' => $_SESSION['user']->id];
$trips = $db->get_results($sql, $data);
