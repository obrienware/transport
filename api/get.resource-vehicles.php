<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Vehicle;

$rows = Vehicle::getAll();
$response = [];
foreach ($rows as $row) {
  $response[] = (object) [
    'id' => 'vehicle-'.$row->id,
    'title' => $row->name,
    'eventBackgroundColor' => $row->color
  ];
}

die(json_encode($response));