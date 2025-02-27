<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Vehicle;
use Generic\Logger;
Logger::logRequest();

$rows = Vehicle::getAll();
$response = [];
foreach ($rows as $row) {
  $response[] = (object) [
    'id' => 'vehicle-'.$row->id,
    'title' => $row->name,
    'eventBackgroundColor' => $row->color
  ];
}

exit(json_encode($response));