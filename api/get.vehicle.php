<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;

$vehicle = new Vehicle($id);
echo json_encode($vehicle);
