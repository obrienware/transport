<?php
/**
 * This script is called every minute. It checks whether there are any 
 * trips or events starting in the next hour and sends a text reminder to the driver(s).
 * 
 * This works because the date/time stored in the database is granular to the minute.
 */

require_once '../autoload.php';

use Transport\{ Database, Event, SMS, Trip, User };

$db = Database::getInstance();

$timezone = new DateTimeZone($_ENV['TZ'] ?? 'UTC');
$nextHour = new DateTime('now +1 hour', $timezone);

// $nextHour = Date('Y-m-d H:i:00', strtotime('now +1 hour'));

$query = "SELECT id FROM trips WHERE start_date = :start_date AND archived IS NULL";
$params = ['start_date' => $nextHour->format('Y-m-d H:i:00')];
if ($rows = $db->get_rows($query, $params)) {
  foreach ($rows as $row) {
    $trip = new Trip($row->id);
    if ($trip->driver) {
      $message = "Reminder: ".$trip->summary." @".Date('g:ia', strtotime($trip->startDate));
      SMS::send($trip->driver->phoneNumber, $message);
    }
  }
}


$query = "SELECT * FROM events WHERE start_date = :start_date AND archived IS NULL";
$params = ['start_date' => $nextHour->format('Y-m-d H:i:00')];
if ($rows = $db->get_rows($query, $params)) {
  foreach ($rows as $row) {
    $event = new Event($row->id);
    if (count($event->drivers) > 0) {
      foreach ($event->drivers as $driverId) {
        $driver = new User($driverId);
        $message = "Reminder: ".$event->name." @".Date('g:ia', strtotime($event->startDate));
        SMS::send($driver->phoneNumber, $message);
      }
    }
  }
}
