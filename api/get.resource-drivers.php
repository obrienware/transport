<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\User;

$rows = User::getDrivers();
$response = [];
foreach ($rows as $row) {
  $response[] = (object) [
    'id' => 'driver-'.$row->id,
    'title' => $row->first_name.' '.$row->last_name,
    // 'eventBackgroundColor' => $row->color
  ];
}

exit(json_encode($response));