<?php
header('Content-Type: application/json');

require_once '../../autoload.php';

use Transport\Flight;

$result = Flight::updateFlight($_GET['flight']);

echo json_encode($result);