<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];

if (isset($_REQUEST['requestorId'])) {
  $criteria = "AND e.requestor_id = {$_REQUEST['requestorId']}";
}

// I want to create a trip class, but in the mean time we'll just pull the data from the database
require_once 'class.data.php';
$db = new data();
$query = "
SELECT 
  e.* 
FROM events e
WHERE
  (
    (e.start_date BETWEEN :start AND :end) OR
    (e.end_date BETWEEN :start AND :end)
  )
  AND e.archived IS NULL
  AND e.cancellation_requested IS NULL
  {$criteria}
";
$params = ['start' => $start, 'end' => $end];


$result = [];
if ($rows = $db->get_rows($query, $params)) {
  foreach ($rows as $row) {
    $resourceIds = [];
    if ($row->driver_ids) {
      $drivers = explode(',', $row->driver_ids);
      foreach($drivers as $driverId) $resourceIds[] = 'driver-'.$driverId;
    }
    if ($row->vehicle_ids) {
      $vehicles = explode(',', $row->vehicle_ids);
      foreach($vehicles as $vehicleId) $resourceIds[] = 'vehicle-'.$vehicleId;
    }
  
    $event = (object) [
      'id' => $row->id,
      'title' => $row->name,
      'resourceIds' => $resourceIds,
      'allDay' => true,
      'start' => $row->start_date,
      'end' => $row->end_date,
      'extendedProps' => [
        'type' => 'event',
        'confirmed' => $row->confirmed,
      ],
      'backgroundColor' => ($row->color) ?: '#AAAAAA'
    ];
    // Format for the requestor's view
    if (isset($_REQUEST['requestorId'])) {
      if ($row->confirmed) {
        $event->backgroundColor = '#03fc30';
        $event->textColor = '#000000';
      } else {
        $event->backgroundColor = '#cccccc';
        $event->textColor = '#000000';
      }
    }
    $result[] = $event;
  }
}

die(json_encode($result));