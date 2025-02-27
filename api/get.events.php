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

if ($requestorId)
{
  $criteria = " AND e.requestor_id = {$requestorId}";
}
if (!$history)
{
  $criteria .= " AND e.end_date >= CURDATE()";
}
if ($onlyMe)
{
  $criteria .= " AND FIND_IN_SET({$_SESSION['user']->id}, e.driver_ids) > 0";
}

// I want to create a trip class, but in the mean time we'll just pull the data from the database
$db = Database::getInstance();
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
if ($rows = $db->get_rows($query, $params))
{
  foreach ($rows as $row)
  {
    $resourceIds = [];
    if ($row->driver_ids)
    {
      $drivers = explode(',', $row->driver_ids);
      foreach ($drivers as $driverId) $resourceIds[] = 'driver-' . $driverId;
    }
    if ($row->vehicle_ids)
    {
      $vehicles = explode(',', $row->vehicle_ids);
      foreach ($vehicles as $vehicleId) $resourceIds[] = 'vehicle-' . $vehicleId;
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
      'backgroundColor' => ($row->confirmed) ? 'lightgreen' : '#dee2e6',
      'textColor' => ($row->confirmed) ? 'black' : 'gray'
    ];
    // Format for the requestor's view
    if (isset($_GET['requestorId']))
    {
      if ($row->confirmed)
      {
        $event->backgroundColor = '#03fc30'; // green
        $event->textColor = '#000000';
      }
      else
      {
        $event->backgroundColor = '#cccccc'; // gray
        $event->textColor = '#000000';
      }
    }
    $result[] = $event;
  }
}

exit(json_encode($result));
