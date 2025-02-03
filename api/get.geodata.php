<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once '../autoload.php';

use Transport\Config;
use Transport\Utils;

$config = Config::get('system');
$keys = $config->keys;

// address | latlng

$placeId = filter_input(INPUT_GET, 'placeId', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$latlng = filter_input(INPUT_GET, 'lanlng', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$address = filter_input(INPUT_GET, 'address', FILTER_SANITIZE_FULL_SPECIAL_CHARS);


if ($placeId) {
  $result = Utils::callApi('GET', 'https://places.googleapis.com/v1/places/'.$placeId, [
    'key' => $keys->GOOGLE_API_KEY,
    'fields' => 'id,formattedAddress,location'
  ]);
  die(json_encode(json_decode($result)));
}

if($latlng) {
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'latlng' => $latlng
  ]);
  die(json_encode(json_decode($result)));
} 

if ($address) {
  $result = Utils::callApi('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
    'key' => $keys->GOOGLE_API_KEY,
    'address' => $address
  ]);  
  die(json_encode(json_decode($result)));
}

die('false');