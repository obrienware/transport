<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Airline;
use Generic\Logger;
Logger::logRequest();

exit(json_encode(Airline::getAll()));