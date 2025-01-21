<?php
header('Content-Type: application/json');

require_once '../../autoload.php';

use Transport\Vehicle;

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$vehicle = new Vehicle($id);
echo json_encode($vehicle);
