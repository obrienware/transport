<pre>
<?php
/**
 * The purpose of this script is to make an API call to fetch flight data for upcoming flights
 * Since we want to minimize the number of API calls we make, we'll re-check on a schedule - the
 * closer the flight is to arriving/departing, the more frequently we'll check.
 */

date_default_timezone_set($_ENV['TZ'] ?? 'America/Denver');
require_once '../autoload.php';

use Transport\{ Database, Flight };

$db = Database::getInstance();

// What this query does is get all the flights that are either arriving or departing today.
// e.g. 2025-02-05 12:05:00, arrival, DEN, UA1234
// Note that the datetimes here represent *local* time
$query = "
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

echo Date('Y-m-d H:i:s').": Checking on today's flights:...\n";

if ($rows = $db->get_rows($query)) {

  echo "Found ".count($rows)." flights for today.\n";

  foreach ($rows as $row) {
    // echo "\n> {$row->target_datetime}: {$row->type} {$row->iata} {$row->flight_number};\n";
    echo "\n> {$row->flight_number} {$row->type} {$row->iata}: ".Date('Y-m-d H:i', strtotime($row->target_datetime)).";\n";

    $lastChecked = Flight::lastChecked($row->flight_number);
    echo "Last checked: {$lastChecked} min(s) ago\n";
    if ($lastChecked === false) {
      // If we haven't checked this flight before, we can check it rn and be done.
      echo "Making an immediate API call to get the latest data on this flight.\n";
      Flight::updateFlight($row->flight_number);
      continue;
    }

    $flight = Flight::getFlightStatus($row->flight_number, $row->type, $row->iata);

    $now = strtotime('now');
    
    if ($row->type == 'arrival') {
      if ($flight->real_arrival) {
        echo "No further calls will be made for this flight since it has already arrived.\n";
        continue; // Flight has already arrived
      }
      $arrival_time = ($flight->estimated_arrival) ? strtotime($flight->estimated_arrival) : strtotime($flight->scheduled_arrival);
      $arrive_in = round(($arrival_time - $now) / 60, 2);

      // If our ETA is less than 15mins then we're re-checking every minute
      if ($arrive_in <= 15) {
        echo "ETA is less than 15mins, re-checking every minute.\n";
        echo "Checking now...\n";
        Flight::updateFlight($row->flight_number);
      } elseif ($arrive_in <= 60) { 
        // If our ETA is less than an hour, then we're re-checking every 10mins
        echo "ETA is less than 60mins, re-checking every 10mins.\n";
        if ($lastChecked >= 10) {
          echo "Checking now...\n";
          Flight::updateFlight($row->flight_number);
        }
      } else {
        // If our ETA is today, then we're re-checking every 60mins
        echo "ETA is today (but not within the next hour), re-checking every 60mins.\n";
        if ($lastChecked >= 60) {
          echo "Checking now...\n";
          Flight::updateFlight($row->flight_number);
        }
      }
    }

    /**
     * When the flight is an outbound one, we want to update the status every 30 mins unless
     * the ETD has already passed.
     */
    if ($row->type == 'departure') {
      if ($flight->real_departure) {
        echo "No further calls will be made for this flight since it has already departed.\n";
        continue; // Flight has already departed
      }
      $departure_time = ($flight->estimated_departure) ? strtotime($flight->estimated_departure) : strtotime($flight->scheduled_departure);
      $departs_in = round(($departure_time - $now) / 60, 2); // minutes

      if ($departs_in > - 90) {
        echo "Re-checking every 15mins.\n";
        // We havn't passed our ETD yet
        if ($lastChecked >= 15) {
          echo "Checking now...\n";
          Flight::updateFlight($row->flight_number);
        }
      }
    }

  }
  echo "\nDone.\n";
  exit;
}
echo "\nNo flights found for today.\n";
