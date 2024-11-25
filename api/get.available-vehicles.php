<?php
header('Content-Type: application/json');
require_once 'class.data.php';
$db = new data();

// We want to exclude current trips and/or current events
$current_tripId = $_REQUEST['tripId'] ?: 0;
$current_eventId = $_REQUEST['eventId'] ?: 0;

$sql = "
SELECT 
  id, name, require_cdl, color
FROM vehicles v
WHERE
	v.archived IS NULL

	-- Look in trips
	AND v.id NOT IN (
		SELECT vehicle_id FROM trips
		WHERE 
			(start_date BETWEEN :from_date AND :to_date
			OR end_date BETWEEN :from_date AND :to_date)
			AND archived IS NULL
			AND id <> :trip_id
			AND vehicle_id IS NOT NULL
	)

	-- Look in events
	AND NOT FIND_IN_SET(v.id, (
		SELECT CASE WHEN GROUP_CONCAT(vehicle_ids) IS NULL THEN 0 ELSE GROUP_CONCAT(vehicle_ids) END FROM events  
		WHERE 
			(start_date BETWEEN :from_date AND :to_date OR end_date BETWEEN :from_date AND :to_date) 
			AND archived IS NULL 
			AND id <> :event_id
	))

ORDER BY name
";
$data = [
  'from_date' => $_REQUEST['startDate'],
  'to_date' => $_REQUEST['endDate'],
	'trip_id' => $current_tripId,
	'event_id' => $current_eventId
];
$result = $db->get_results($sql, $data);

// TODO: Check whether the vehicle will be in the shop for maintenance at this time

die(json_encode($result));