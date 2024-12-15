<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.sms.php';
require_once 'class.data.php';
require_once 'class.trip.php';
require_once 'class.event.php';

$nextHour = Date('Y-m-d H:i:00', strtotime('now +1 hour'));

$sql = "SELECT id FROM trips WHERE start_date = :start_date AND archived IS NULL";
$data = ['start_date' => $nextHour];
if ($rs = $db->get_results($sql, $data)) {
  foreach ($rs as $item) {
    $trip = new Trip($item->id);
    if ($trip->driver) {
      $message = "Reminder: ".$trip->summary." @".Date('g:ia', strtotime($trip->startDate));
      SMS::send($trip->driver->phoneNumber, $message);
    }
  }
}


$sql = "SELECT * FROM events WHERE start_date = :start_date AND archived IS NULL";
$data = ['start_date' => $nextHour];
if ($rs = $db->get_results($sql, $data)) {
  foreach ($rs as $item) {
    $event = new Event($item->id);
    if (count($event->drivers) > 0) {
      foreach ($event->drivers as $driverId) {
        $driver = new User($driverId);
        $message = "Reminder: ".$event->name." @".Date('g:ia', strtotime($event->startDate));
        SMS::send($driver->phoneNumber, $message);
      }
    }
  }
}
