<?php
declare(strict_types=1);
namespace Transport;

require_once __DIR__.'/../../autoload.php';

use DateTime;
use DateTimeZone;

class Flight
{

  static function getFlightStatus(string $flightNumber, string $type, string $iata, ?string $date = NULL): object | false
  {
    $db = Database::getInstance();
    if (!$date) $date = Date('Y-m-d'); // Default to today
    if ($type == 'arrival') {
      $query = "
        SELECT * FROM flight_data 
        WHERE 
          DATE(scheduled_arrival) = :date
          AND airport_destination_iata = :iata
          AND flight_number = :flight_number
      ";
    } else { // type == 'departure'
      $query = "
        SELECT * FROM flight_data 
        WHERE 
          DATE(scheduled_departure) = :date
          AND airport_origin_iata = :iata
          AND flight_number = :flight_number
      ";
    }
    $params = [
      'date' => $date,
      'iata' => $iata,
      'flight_number' => $flightNumber
    ];
    return $db->get_row($query, $params);
  }

  
  /**
   * This will get the latest data for the specified flight and populate our flight_data table
   * The remote call returns flight details for multiple days (both past, present and future). 
   * We'll aggregate all of it
   */
  static function updateFlight(string $flightNumber): bool
  {
    date_default_timezone_set($_ENV['TZ'] ?? 'America/Denver');
    $db = Database::getInstance();
    $keys = Config::get('system')->keys;
    $db->query(
      "REPLACE INTO _flight_check SET flight_number = :flight_number, last_checked = NOW()",
      ['flight_number' => $flightNumber]
    );
    $url ='https://flight-radar1.p.rapidapi.com/flights/get-more-info';
    $result = Utils::callApi('GET', $url, 
      [
        'fetchBy' => 'flight',
        'query' => $flightNumber
      ], null,
      [
        'X-Rapidapi-Key: '.$keys->FLIGHT_RADAR_API,
        'X-Rapidapi-Host: flight-radar1.p.rapidapi.com'
      ]
    );
    $obj = json_decode($result);
    if ($obj->result->response->data) {
      foreach ($obj->result->response->data as $flight) {
        $query = "
          REPLACE INTO flight_data SET
            row = :row,
            flight_number = :flight_number,
            status_live = :status_live,
            status_text = :status_text,
            status_icon = :status_icon,
            airport_origin = :airport_origin,
            airport_origin_iata = :airport_origin_iata,
            scheduled_departure = :scheduled_departure,
            estimated_departure = :estimated_departure,
            real_departure = :real_departure,
            airport_destination = :airport_destination,
            airport_destination_iata = :airport_destination_iata,
            scheduled_arrival = :scheduled_arrival,
            estimated_arrival = :estimated_arrival,
            real_arrival = :real_arrival,
            updated = :updated
        ";
        $params = [
          'row' => $flight->identification->row,
          'flight_number' => $flight->identification->number->default,
          'status_live' => $flight->status->live ? 1 : 0,
          'status_text' => $flight->status->text,
          'status_icon' => $flight->status->icon,
          'airport_origin' => $flight->airport->origin->name,
          'airport_origin_iata' => $flight->airport->origin->code->iata,
          'scheduled_departure' => $flight->time->scheduled->departure ? Date('Y-m-d H:i:s', $flight->time->scheduled->departure) : NULL,
          'estimated_departure' => $flight->time->estimated->departure ? Date('Y-m-d H:i:s', $flight->time->estimated->departure) : NULL,
          'real_departure' => $flight->time->real->departure ? Date('Y-m-d H:i:s', $flight->time->real->departure) : NULL,
          'airport_destination' => $flight->airport->destination->name,
          'airport_destination_iata' => $flight->airport->destination->code->iata,
          'scheduled_arrival' => $flight->time->scheduled->arrival ? Date('Y-m-d H:i:s', $flight->time->scheduled->arrival) : NULL,
          'estimated_arrival' => $flight->time->estimated->arrival ? Date('Y-m-d H:i:s', $flight->time->estimated->arrival) : NULL,
          'real_arrival' => $flight->time->real->arrival ? Date('Y-m-d H:i:s', $flight->time->real->arrival) : NULL,
          'updated' => $flight->time->other->updated ? Date('Y-m-d H:i:s', $flight->time->other->updated) : NULL,
        ];
        $db->query($query, $params);
      }
    }
    return true;
  }

  /**
   * Returns the number of minutes since the last check for this flight, or false if never checked
   */
  static function lastChecked(string $flightNumber): int | bool
  {
    date_default_timezone_set($_ENV['TZ'] ?? 'America/Denver');
    $db = Database::getInstance();
    $lastChecked = $db->get_var(
      "SELECT last_checked FROM _flight_check WHERE flight_number = :flight_number",
      ['flight_number' => $flightNumber]
    );
    if (!$lastChecked) return false;
    $time1 = strtotime('now');
    $time2 = strtotime($lastChecked);
    return round(abs($time1 - $time2) / 60, 2);
  }

  public static function upcomingFlights(): array | false
  {
    $db = Database::getInstance();
    $query = "
      SELECT 
        t.summary, t.guests,
        t.pickup_date,
        CASE WHEN t.ETA IS NOT NULL THEN t.ETA ELSE t.ETD END AS target_datetime,
        CASE WHEN t.ETA IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
        CASE WHEN t.ETA IS NOT NULL THEN a.iata ELSE b.iata END AS iata,
        CONCAT(l.flight_number_prefix, t.flight_number) AS flight_number,
        l.name AS airline,
        l.image_filename,
        d.first_name AS driver
      FROM trips t
      LEFT OUTER JOIN airlines l ON l.id = t.airline_id
      LEFT OUTER JOIN locations a ON a.id = t.pu_location
      LEFT OUTER JOIN locations b ON b.id = t.do_location
      LEFT OUTER JOIN users d ON d.id = t.driver_id
      WHERE
        (t.eta IS NOT NULL OR t.etd IS NOT NULL)
        AND
        (t.eta IS NULL OR DATE(eta) >= CURDATE())
        AND
        (t.etd IS NULL OR DATE(etd) >= CURDATE())	
        AND t.archived IS NULL
        AND DATE(t.pickup_date) < DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- Looking 7 days ahead
      ORDER BY COALESCE(t.eta, t.etd) -- This is brilliant! Orders by either ETA OR ETD where the other is NULL!
    ";
    return $db->get_rows($query);
  }
}