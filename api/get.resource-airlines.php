<?php
header('Content-Type: application/json');
require_once 'class.airline.php';
die(json_encode(Airline::getAll()));