<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$start = $_REQUEST['start'];
$end = $_REQUEST['end'];

if (isset($_REQUEST['requestorId'])) {
  $criteria = "AND t.requestor_id = {$_REQUEST['requestorId']}";
}

// I want to create a trip class, but in the mean time we'll just pull the data from the database
require_once 'class.data.php';
$db = new data();
$sql = "
SELECT 
  t.*, v.color, v.name as vehicle,
  CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_location,
  CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff_location,
  CONCAT(g.first_name,' ',g.last_name) AS guest,
  CONCAT(d.first_name,' ',d.last_name) AS driver
FROM trips t
LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
LEFT OUTER JOIN locations pu ON pu.id = t.pu_location
LEFT OUTER JOIN locations do ON do.id = t.do_location
LEFT OUTER JOIN guests g ON g.id = t.guest_id
LEFT OUTER JOIN users d ON d.id = t.driver_id
WHERE
  (
    (start_date BETWEEN :start AND :end) OR
    (end_date BETWEEN :start AND :end)
  )
  AND t.archived IS NULL
  AND t.cancellation_requested IS NULL
  {$criteria}
";
$data = ['start' => $start, 'end' => $end];

// $sql = "SELECT * FROM trips";
// $data = [];


$result = [];
if ($rs = $db->get_results($sql, $data)) {
  foreach ($rs as $item) {
    $bgColor = ($item->color) ?: '#AAAAAA';
    $textColor = '#'.readableColor($bgColor);
    $event = (object) [
      'id' => $item->id,
      'title' => $item->summary.' @'.Date('g:ia', strtotime($item->pickup_date)),
      'resourceIds' => ["vehicle-{$item->vehicle_id}", "driver-{$item->driver_id}"],
      'allDay' => false,
      'start' => $item->start_date,
      'end' => $item->end_date ?: Date('Y-m-d 23:59:59', strtotime($item->start_date)),
      'extendedProps' => [
        'type' => 'trip',
        'guest' => $item->guest,
        'pickup' => $item->pickup_location,
        'dropoff' => $item->dropoff_location,
        'vehicle' => $item->vehicle,
        'driver' => $item->driver,
        'confirmed' => $item->confirmed
      ],
      'backgroundColor' => $bgColor,
      'textColor' => $textColor
    ];
    // Format for the requestor's view
    if (isset($_REQUEST['requestorId'])) {
      if ($item->confirmed) {
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

function readableColor($bg)
{
  $bg = str_replace('#', '', $bg);
  $r = hexdec(substr($bg, 0, 2));
  $g = hexdec(substr($bg, 2, 2));
  $b = hexdec(substr($bg, 4, 2));

  $squared_contrast = (
    $r * $r * .299 +
    $g * $g * .587 +
    $b * $b * .114
  );

  if ($squared_contrast > pow(170, 2)) {
    return '000000';
  } else {
    return 'FFFFFF';
  }
}