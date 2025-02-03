<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;

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
			(start_date BETWEEN :from_date AND :to_date
			OR end_date BETWEEN :from_date AND :to_date)
			AND archived IS NULL
			AND id <> :maintenance_id
			AND vehicle_id IS NOT NULL
	)	
";

$current_tripId = (int) filter_input(INPUT_GET, 'tripId', FILTER_SANITIZE_NUMBER_INT);
$current_eventId = (int) filter_input(INPUT_GET, 'eventId', FILTER_SANITIZE_NUMBER_INT);
$current_reservationId = (int) filter_input(INPUT_GET, 'reservationId', FILTER_SANITIZE_NUMBER_INT);
$current_maintenanceId = (int) filter_input(INPUT_GET, 'maintenanceId', FILTER_SANITIZE_NUMBER_INT);

$params = [
	'from_date' => filter_input(INPUT_GET, 'startDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
	'to_date' => filter_input(INPUT_GET, 'endDate', FILTER_SANITIZE_FULL_SPECIAL_CHARS),
	'trip_id' => $current_tripId,
	'event_id' => $current_eventId,
	'reservation_id' => $current_reservationId,
	'maintenance_id' => $current_maintenanceId,
];
$ids = $db->get_var($query, $params);
$arrayIds = $ids ? explode(',',$ids) : [];

$query = "
	SELECT
		id, name, require_cdl, color
	FROM vehicles v
	WHERE
		v.archived IS NULL
	ORDER BY name
";
$rows = $db->get_rows($query);
foreach ($rows as $key => $row) {
	$rows[$key]->available = (array_search($row->id, $arrayIds) !== false);
}

die(json_encode($rows));