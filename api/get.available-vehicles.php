<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;
use Generic\InputHandler;

$db = Database::getInstance();

// We want to exclude current trips and/or current events
$query = "
SELECT GROUP_CONCAT(id) FROM vehicles v
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
			(start_date BETWEEN :from_date AND :to_date 
			OR end_date BETWEEN :from_date AND :to_date) 
			AND archived IS NULL 
			AND id <> :event_id
	))

	-- Look in reservations
	AND v.id NOT IN (
		SELECT vehicle_id FROM vehicle_reservations
		WHERE 
			(start_datetime BETWEEN :from_date AND :to_date
			OR end_datetime BETWEEN :from_date AND :to_date)
			AND archived IS NULL
			AND id <> :reservation_id
			AND vehicle_id IS NOT NULL
	)

	-- Look in maintenance
	AND v.id NOT IN (
		SELECT vehicle_id FROM vehicle_maintenance
		WHERE 
			(start_datetime BETWEEN :from_date AND :to_date
			OR end_datetime BETWEEN :from_date AND :to_date)
			AND archived IS NULL
			AND id <> :maintenance_id
			AND vehicle_id IS NOT NULL
	)	
";

$current_tripId = InputHandler::getInt(INPUT_GET, 'tripId');
$current_eventId = InputHandler::getInt(INPUT_GET, 'eventId');
$current_reservationId = InputHandler::getInt(INPUT_GET, 'reservationId');
$current_maintenanceId = InputHandler::getInt(INPUT_GET, 'maintenanceId');

$startDate = InputHandler::getString(INPUT_GET, 'startDate');
$endDate = InputHandler::getString(INPUT_GET, 'endDate');

$params = [
	'from_date' => $startDate,
	'to_date' => $endDate,
	'trip_id' => $current_tripId,
	'event_id' => $current_eventId,
	'reservation_id' => $current_reservationId,
	'maintenance_id' => $current_maintenanceId,
];
$ids = $db->get_var($query, $params);
$arrayIds = $ids ? explode(',', $ids) : [];

$query = "
	SELECT
		id, name, require_cdl, color
	FROM vehicles v
	WHERE
		v.archived IS NULL
	ORDER BY name
";
$rows = $db->get_rows($query);
foreach ($rows as $key => $row)
{
	$rows[$key]->available = (array_search($row->id, $arrayIds) !== false);
}

exit(json_encode($rows));
