<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$rs = Vehicle::getVehicles();
$response = [];
foreach ($rs as $item) {
  $response[] = (object) [
    'id' => 'vehicle-'.$item->id,
    'title' => $item->name,
    'eventBackgroundColor' => $item->color
  ];
}

die(json_encode($response));