<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
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