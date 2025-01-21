<?php
header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;

die(json_encode(Airline::getAll()));