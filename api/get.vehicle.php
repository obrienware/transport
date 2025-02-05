<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Vehicle;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');

$vehicle = new Vehicle($id);
echo json_encode($vehicle);
