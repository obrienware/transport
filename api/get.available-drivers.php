<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

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
			(:from_date BETWEEN start_date AND end_date
			OR :to_date BETWEEN start_date AND end_date)
			AND archived IS NULL
			AND id <> :trip_id
			AND driver_id IS NOT NULL
	)

	-- Look in events
	AND NOT FIND_IN_SET(u.id, (
		SELECT CASE WHEN GROUP_CONCAT(driver_ids) IS NULL THEN 0 ELSE GROUP_CONCAT(driver_ids) END FROM events  
		WHERE 
			(:from_date BETWEEN start_date AND end_date
			OR :to_date BETWEEN start_date AND end_date)
			AND archived IS NULL 
			AND id <> :event_id
	))

	-- Look at block outs
	AND u.id NOT IN (
		SELECT user_id FROM user_blockouts
		WHERE 
			(:from_date BETWEEN from_datetime AND to_datetime
			OR :to_date BETWEEN from_datetime AND to_datetime)
			AND archived IS NULL
	)
";

$current_tripId = InputHandler::getInt(INPUT_GET, 'tripId') ?? 0;
$current_eventId = InputHandler::getInt(INPUT_GET, 'eventId') ?? 0;

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
foreach ($rows as $key => $row)
{
	$rows[$key]->available = (array_search($row->id, $arrayIds) !== false);
}

exit(json_encode($rows));
