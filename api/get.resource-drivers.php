<?php
header('Content-Type: application/json');
require_once 'class.user.php';
$rs = User::getDrivers();
$response = [];
foreach ($rs as $item) {
  $response[] = (object) [
    'id' => 'driver-'.$item->id,
    'title' => $item->first_name.' '.$item->last_name,
    // 'eventBackgroundColor' => $item->color
  ];
}

die(json_encode($response));