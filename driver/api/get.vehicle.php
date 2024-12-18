<?php
header('Content-Type: application/json');
require_once 'class.vehicle.php';
$vehicle = new Vehicle($_REQUEST['id']);
echo json_encode($vehicle);
