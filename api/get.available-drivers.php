<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();

// We want to exclude current trips and/or current events
$current_tripId = $_REQUEST['tripId'] ?: 0;
$current_eventId = $_REQUEST['eventId'] ?: 0;

$sql = "
SELECT 
  id, CONCAT(first_name,' ',last_name) AS driver, cdl 
FROM users u
WHERE
	FIND_IN_SET('driver', roles) -- we only want drivers
	AND u.archived IS NULL

	-- Look in trips
	AND u.id NOT IN (
		SELECT driver_id FROM trips
		WHERE 
			(start_date BETWEEN :from_date AND :to_date
			OR end_date BETWEEN :from_date AND :to_date)
			AND archived IS NULL
			AND id <> :trip_id
			AND driver_id IS NOT NULL
	)

	-- Look in events
	AND NOT FIND_IN_SET(u.id, (
		SELECT CASE WHEN GROUP_CONCAT(driver_ids) IS NULL THEN 0 ELSE GROUP_CONCAT(driver_ids) END FROM events  
		WHERE 
			(start_date BETWEEN :from_date AND :to_date OR end_date BETWEEN :from_date AND :to_date) 
			AND archived IS NULL 
			AND id <> :event_id
	))

	-- Look at block outs
	AND u.id NOT IN (
		SELECT user_id FROM user_blockouts
		WHERE 
			(from_datetime BETWEEN :from_date AND :to_date
			OR to_datetime BETWEEN :from_date AND :to_date)
			AND archived IS NULL
	)

ORDER BY first_name, last_name
";
$data = [
  'from_date' => $_REQUEST['startDate'],
  'to_date' => $_REQUEST['endDate'],
	'trip_id' => $current_tripId,
	'event_id' => $current_eventId
];
die(json_encode($db->get_results($sql, $data)));