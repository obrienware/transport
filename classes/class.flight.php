<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.utils.php';
require_once 'class.data.php';
if (!isset($db)) $db = new data();

class Flight
{

  static function getFlightStatus($flightNumber, $type, $iata, $date = NULL)
  {
    global $db;
    if (!$date) $date = Date('Y-m-d'); // Default to today
    if ($type == 'arrival') {
      $sql = "
        SELECT * FROM flight_data 
        WHERE 
          DATE(scheduled_arrival) = :date
          AND airport_destination_iata = :iata
          AND flight_number = :flight_number
      ";
    } else {
      $sql = "
        SELECT * FROM flight_data 
        WHERE 
          DATE(scheduled_departure) = :date
          AND airport_origin_iata = :iata
          AND flight_number = :flight_number
      ";
    }
    $data = [
      'date' => $date,
      'iata' => $iata,
      'flight_number' => $flightNumber
    ];
    return $db->get_row($sql, $data);
  }

  /**
   * This will get the latest data for the specified flight and populate our flight_data table
   * The remote call returns flight details for multiple days (both past, present and future). We'll aggregate all of it
   */
  static function updateFlight($flightNumber) 
  {
    global $db;
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
        'X-Rapidapi-Key: '.$_ENV['FLIGHT_RADAR_API'],
        'X-Rapidapi-Host: flight-radar1.p.rapidapi.com'
      ]
    );
    $obj = json_decode($result);
    if ($obj->result->response->data) {
      foreach ($obj->result->response->data as $flight) {
        $sql = "
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
        $data = [
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
        $db->query($sql, $data);
      }
    }
    return true;
  }

  /**
   * Returns the number of minutes since the last check for this flight, or false if never checked
   */
  static function lastChecked($flightNumber)
  {
    global $db;
    $lastChecked = $db->get_var(
      "SELECT last_checked WHERE flight_number = :flight_number",
      ['flight_number' => $flightNumber]
    );
    if (!$lastChecked) return false;
    $time1 = strtotime('now');
    $time2 = strtotime($lastChecked);
    return round(abs($time1 - $time2) / 60, 2);
  }
}