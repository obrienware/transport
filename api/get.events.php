<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];

// I want to create a trip class, but in the mean time we'll just pull the data from the database
require_once 'class.data.php';
$db = new data();
$sql = "
SELECT 
  e.* 
FROM events e
WHERE
  (
    (e.start_date BETWEEN :start AND :end) OR
    (e.end_date BETWEEN :start AND :end)
  )
  AND e.archived IS NULL
";
$data = ['start' => $start, 'end' => $end];


$result = [];
if ($rs = $db->get_results($sql, $data)) {
  foreach ($rs as $item) {
    $resourceIds = [];
    if ($item->driver_ids) {
      $drivers = explode(',', $item->driver_ids);
      foreach($drivers as $driverId) $resourceIds[] = 'driver-'.$driverId;
    }
    if ($item->vehicle_ids) {
      $vehicles = explode(',', $item->vehicle_ids);
      foreach($vehicles as $vehicleId) $resourceIds[] = 'vehicle-'.$vehicleId;
    }
  
    $event = (object) [
      'id' => $item->id,
      'title' => $item->name,
      'resourceIds' => $resourceIds,
      'allDay' => true,
      'start' => $item->start_date,
      'end' => $item->end_date,
      'extendedProps' => [
        'type' => 'event',
      ],
      'backgroundColor' => ($item->color) ?: '#AAAAAA'
    ];
    $result[] = $event;
  }
}

die(json_encode($result));