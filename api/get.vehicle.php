<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
if ($id) $id = (int)$id;

$vehicle = new Vehicle($id);
echo json_encode($vehicle);
