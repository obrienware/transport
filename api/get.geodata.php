<?php

declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Generic\{InputHandler, Logger};

Logger::logRequest();

$config = Config::get('system');
$keys = $config->keys;

// osmType | osmId | latlng | address

$osmType = InputHandler::getString(INPUT_GET, 'osmType');
$osmId = InputHandler::getString(INPUT_GET, 'osmId');
$latlng = InputHandler::getString(INPUT_GET, 'latlng');
$address = InputHandler::getString(INPUT_GET, 'address');

function fetchFromUrl(string $url): array
{
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MyTransportApp/0.1 (richard@obrienware.com)'
  ]);
  $response = curl_exec($ch);
  if ($response === false)
  {
    return [
      'result' => false,
      'error' => 'Failed to get data from OSM',
      'url' => $url
    ];
  }
  curl_close($ch);

  $data = json_decode($response);
  if (json_last_error() !== JSON_ERROR_NONE)
  {
    return [
      'result' => false,
      'error' => 'Failed to parse JSON response from OSM',
      'response' => $response
    ];
  }
  if (is_array($data) && count($data) > 0)
  {
    $data = $data[0];
  }
  return [
    'result' => true,
    'data' => $data,
    'url' => $url,
    'response' => $response
  ];
}

if ($osmType && $osmId)
{
  $osmType = strtoupper($osmType[0]);
  $url = "https://nominatim.openstreetmap.org/details.php?osmtype={$osmType}&osmid={$osmId}&format=json";
  $result = fetchFromUrl($url);
  exit(json_encode($result));
}

if ($latlng)
{
  [$lat, $lon] = explode(',', $latlng);
  $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lon}&format=json";
  $result = fetchFromUrl($url);
  exit(json_encode($result));
}

if ($address)
{
  $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address);
  $result = fetchFromUrl($url);
  exit(json_encode($result));
}

exit(json_encode([
  'result' => false,
  'error' => 'Invalid request'
]));
