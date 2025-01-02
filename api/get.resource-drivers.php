<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$rows = User::getDrivers();
$response = [];
foreach ($rows as $row) {
  $response[] = (object) [
    'id' => 'driver-'.$row->id,
    'title' => $row->first_name.' '.$row->last_name,
    // 'eventBackgroundColor' => $row->color
  ];
}

die(json_encode($response));