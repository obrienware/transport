<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Generic\{ InputHandler, Utils };
use Generic\Logger;
Logger::logRequest();

$config = Config::get('system');
$keys = $config->keys;

// address | latlng

$placeId = InputHandler::getString(INPUT_GET, 'placeId');
$latlng = InputHandler::getString(INPUT_GET, 'lanlng');
$address = InputHandler::getString(INPUT_GET, 'address');


if ($placeId)
{
  $result = Utils::callApi('GET', 'https://places.googleapis.com/v1/places/' . $placeId, [
    'key' => $keys->GOOGLE_API_KEY,
    'fields' => 'id,formattedAddress,location'
  ]);
  exit(json_encode(json_decode($result)));
}

if ($latlng)
{
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'latlng' => $latlng
  ]);
  exit(json_encode(json_decode($result)));
}

if ($address)
{
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'address' => $address
  ]);
  exit(json_encode(json_decode($result)));
}

exit('false');
