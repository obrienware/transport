<?php
header('Content-Type: application/json');
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

require_once 'class.flight.php';
require_once 'class.data.php';

// We want to know about flights happening today
$db = new data();
$sql = "
SELECT 
  CASE WHEN t.ETA IS NOT NULL THEN t.ETA ELSE t.ETD END AS target_datetime,
  CASE WHEN t.ETA IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
  CASE WHEN t.ETA IS NOT NULL THEN a.iata ELSE b.iata END AS iata,
  CONCAT(l.flight_number_prefix, t.flight_number) AS flight_number
FROM trips t
LEFT OUTER JOIN airlines l ON l.id = airline_id
LEFT OUTER JOIN locations a ON a.id = t.pu_location
LEFT OUTER JOIN locations b ON b.id = t.do_location
WHERE
	((t.ETA IS NOT NULL AND DATE(t.ETA) = CURDATE())
	OR
	(t.ETD IS NOT NULL AND DATE(t.ETD) = CURDATE()))
  AND t.archived IS NULL
";
if ($rs = $db->get_results($sql)) {
  foreach ($rs as $item) {
    $lastChecked = Flight::lastChecked($item->flight_number);
    if ($lastChecked === false) {
      // If we haven't checked this flight before, we can check it rn and be done.
      Flight::updateFlight($item->flight_number);
      continue;
    }

    $flight = Flight::getFlightStatus($item->flight_number, $item->type, $item->iata);

    $now = strtotime('now');
    
    if ($item->type == 'arrival') {
      if ($flight->real_arrival) continue; // Flight has already arrived
      $arrival_time = ($flight->estimated_arrival) ? strtotime($flight->estimated_arrival) : strtotime($flight->scheduled_arrival);
      $arrive_in = round(($arrival_time - $now) / 60, 2);

      // If our ETA is less than 15mins then we're re-checking every minute
      if ($arrive_in <= 15) {
        Flight::updateFlight($item->flight_number);
      } elseif ($arrive_in <= 60) { 
        // If our ETA is less than an hour, then we're re-checking every 10mins
        if ($lastChecked >= 10) Flight::updateFlight($item->flight_number);
      } else {
        // If our ETA is today, then we're re-checking every 60mins
        if ($lastChecked >= 60) Flight::updateFlight($item->flight_number);
      }
    }

    /**
     * When the flight is an outbound one, we want to update the status every 30 mins unless
     * the ETD has already passed.
     */
    if ($item->type == 'departure') {
      if ($flight->real_departure) continue; // Flight has already departed
      $departure_time = ($flight->estimated_departure) ? strtotime($flight->estimated_departure) : strtotime($flight->scheduled_departure);
      $departs_in = round(($departure_time - $now) / 60, 2);

      if ($departs_in > 0) {
        // We havn't passed our ETD yet
        if ($lastChecked >= 30) Flight::updateFlight($item->flight_number);
      }
    }

  }
}