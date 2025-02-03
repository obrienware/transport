<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;

$db = Database::getInstance();

// We want to exclude current trips and/or current events
$query = "
SELECT GROUP_CONCAT(id) FROM users u
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
			(start_date BETWEEN :from_date AND :to_date 
			OR end_date BETWEEN :from_date AND :to_date) 
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
";

$current_tripId = (int) filter_input(INPUT_GET, 'tripId', FILTER_SANITIZE_NUMBER_INT);
$current_eventId = (int) filter_input(INPUT_GET, 'eventId', FILTER_SANITIZE_NUMBER_INT);

$params = [
	'from_date' => filter_input(INPUT_GET, 'startDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
	'to_date' => filter_input(INPUT_GET, 'endDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
	'trip_id' => $current_tripId,
	'event_id' => $current_eventId
];
$ids = $db->get_var($query, $params);
$arrayIds = $ids ? explode(',', $ids) : [];

$query = "
	SELECT 
		id, CONCAT(first_name,' ',last_name) AS driver, cdl 
	FROM users u
	WHERE
		FIND_IN_SET('driver', roles) -- we only want drivers
		AND u.archived IS NULL
	ORDER BY first_name, last_name
";
$rows = $db->get_rows($query);
foreach ($rows as $key => $row) {
	$rows[$key]->available = (array_search($row->id, $arrayIds) !== false);
}

die(json_encode($rows));