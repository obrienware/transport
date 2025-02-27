<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Database;
use Generic\InputHandler;
use Generic\Logger;
Logger::logRequest();

$start = InputHandler::getString(INPUT_GET, 'start');
$end = InputHandler::getString(INPUT_GET, 'end');
$requestorId = InputHandler::getInt(INPUT_GET, 'requestorId');
$history = InputHandler::getBool(INPUT_GET, 'history');
$onlyMe = InputHandler::getBool(INPUT_GET, 'onlyMe');

if ($onlyMe) exit('[]');

if ($requestorId) {
  $criteria = " AND r.requestor_id = {$requestorId}";
}
if (!$history) {
  $criteria .= " AND r.end_datetime >= CURDATE() -- Only show future trips (cleaner UI)";
}

$db = Database::getInstance();
$query = "
  SELECT 

    r.*, v.color, v.name as vehicle,
    CONCAT(g.first_name,' ',g.last_name) AS guest

  FROM vehicle_reservations r
  LEFT OUTER JOIN vehicles v ON v.id = r.vehicle_id
  LEFT OUTER JOIN guests g ON g.id = r.guest_id

  WHERE
    (
      (r.start_datetime BETWEEN :start AND :end) OR
      (r.end_datetime BETWEEN :start AND :end)
    )
    AND r.archived IS NULL
    {$criteria}
";
$params = ['start' => $start, 'end' => $end];


$result = [];
if ($rows = $db->get_rows($query, $params)) {
  foreach ($rows as $row) {
    $bgColor = ($row->color) ?: '#AAAAAA';
    $textColor = '#'.readableColor($bgColor);
    $event = (object) [
      'id' => $row->id,
      'title' => $row->guest.' use of vehicle: '.$row->vehicle,
      'resourceIds' => ["vehicle-{$row->vehicle_id}"],
      'allDay' => false,
      'start' => $row->start_datetime,
      'end' => $row->end_datetime,
      'extendedProps' => [
        'type' => 'reservation',
        'guest' => $row->guest,
        'vehicle' => $row->vehicle,
        'confirmed' => $row->confirmed
      ],
      'backgroundColor' => ($row->confirmed) ? $bgColor : '#dee2e6',
      'textColor' => ($row->confirmed) ? $textColor : 'gray'
    ];
    // Format for the requestor's view
    if ($requestorId) {
      if ($row->confirmed) {
        $event->backgroundColor = '#03fc30'; // green
        $event->textColor = '#000000';
      } else {
        $event->backgroundColor = '#cccccc'; // gray
        $event->textColor = '#000000';
      }
    }
    $result[] = $event;
  }
}

exit(json_encode($result));

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

  if ($squared_contrast > pow(170, 2)) return '000000';
  return 'FFFFFF';
}